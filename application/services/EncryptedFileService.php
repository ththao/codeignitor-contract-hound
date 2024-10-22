<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use Aws\S3\S3Client;

class EncryptedFileService extends Service {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'EncryptedFileModel';

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Purge stored contract file from archive folder
	 *
	 * @access protected
	 * @param ContractModel $oContract
	 * @return bool
	 */
	protected function _purgeCachedContractFile(EncryptedFileModel $oFile) {
		$sKey = $oFile->file_hash;

		$sSubPath = substr($sKey,0,1);
		$sSubPath .= '/'.substr($sKey,1,1);
		$sSubPath .= '/'.substr($sKey,2,1);
		$sFullPath = $sSubPath.'/'.$sKey;
		
		$sS3Bucket = ConfigService::getItem('s3bucket');

		// store
		$oAWSClient = S3Client::factory(array(
			'profile'  => $sS3Bucket
			,'region'  => 'us-east-1'
			,'version' => '2006-03-01'
		));

		$bResult = $oAWSClient->deleteObject(array(
		    // Bucket is required
		    'Bucket' => $sS3Bucket
		    // Key is required
		    ,'Key'   => $sFullPath
		));
		
		return $bResult;
	}

	/**
	 * Store actual file to archive
	 *
	 * @throws Exception
	 * @param ContractModel $oContract
	 * @param string $sCurrentFileLocation
	 * @return bool
	 */
	public function storeFile(EncryptedFileModel $oFile,$sCurrentFileLocation) {
		if (!file_exists($sCurrentFileLocation)) {
			throw new Exception('File not found at '.$sCurrentFileLocation);
		}

		$sKey = $oFile->file_hash;
		$sFullPath = substr($sKey,0,1).'/'.substr($sKey,1,1).'/'.substr($sKey,2,1).'/'.$sKey;
		$sS3Bucket = ConfigService::getItem('s3bucket');

		// encrypt
		$sPlainText = file_get_contents($sCurrentFileLocation);
		$sEncodeKey = md5('superRoper%&#@$72345'.$oFile->file_hash.md5($oFile->key_id));
		$sCipher = "AES-128-CBC";
		$iIvlen = openssl_cipher_iv_length($sCipher);
		$blIv = openssl_random_pseudo_bytes($iIvlen);

		$sCiphertextRaw = openssl_encrypt($sPlainText, $sCipher, $sEncodeKey, OPENSSL_RAW_DATA, $blIv);
		$mHmac = hash_hmac('sha256', $sCiphertextRaw, $sEncodeKey, true);
		$sCipherText = base64_encode( $blIv.$mHmac.$sCiphertextRaw );
		file_put_contents($sCurrentFileLocation,$sCipherText);

		$oFile->ivlen = $iIvlen;
		$oFile->iv = $blIv;

		// store
		$oAWSClient = S3Client::factory(array(
			'profile'  => $sS3Bucket
			,'region'  => 'us-east-1'
			,'version' => '2006-03-01'
		));

		$bResult = $oAWSClient->putObject(array(
			'Bucket'     => $sS3Bucket,
			'Key'        => $sFullPath,
			'SourceFile' => $sCurrentFileLocation,
			'ACL'        => 'private'
		));
		
		/*if (!$bResult) {
			log_message('error',__METHOD__.' failed to store '.$sFullPath.' '.$oFile->key_id);
		} else {
			log_message('required',__METHOD__.' success to store '.$sFullPath.' '.$oFile->key_id);
		}*/

		// We can poll the object until it is accessible
		$oAWSClient->waitUntil('ObjectExists', array(
			'Bucket' => $sS3Bucket,
			'Key'    => $sFullPath
		));

		$this->updateFile($oFile);

		@unlink($sCurrentFileLocation);

		return true;
	}

	public function retrieveFile(EncryptedFileModel $oFile) {
		$sS3Bucket = ConfigService::getItem('s3bucket');
		$iMemberId = $oFile->owner_id;
		// store
		$oAWSClient = S3Client::factory(array(
			'profile'  => $sS3Bucket
			,'region'  => 'us-east-1'
			,'version' => '2006-03-01'
		));
		
		$sFilename = $oFile->file_hash;
		$sFullPath = substr($sFilename,0,1).'/'.substr($sFilename,1,1).'/'.substr($sFilename,2,1).'/'.$sFilename;
		$bResult = $oAWSClient->doesObjectExist($sS3Bucket,$sFullPath);
		if (!$bResult) {
			return false;
		}
		
		$sTmpPath = '/tmp/'.$iMemberId.'_'.$sFilename;
		
		$bResult = $oAWSClient->getObject(array(
			'Bucket' => $sS3Bucket,
			'Key'    => $sFullPath,
			'SaveAs' => $sTmpPath
		));
		
		if (!$bResult) {
			log_message('required','failed to download file from s3: '.$oFile->contract_id);
			return false;
		}

		$iKey = $oFile->contract_id;
		$sHash = $oFile->file_hash;
		$iIvLen = $oFile->ivlen;
		$blIv = $oFile->iv;
		$sFileName = $oFile->file_name;

		$sEncodeKey = md5('superRoper%&#@$72345'.$sHash.md5($iKey));
		
		$sCipherText = file_get_contents($sTmpPath);
		$sCipherText = base64_decode($sCipherText);
		
		$sCiphertextRaw = substr($sCipherText, $iIvLen + 32);
		$sOriginalPlaintext = openssl_decrypt($sCiphertextRaw, 'AES-128-CBC', $sEncodeKey, OPENSSL_RAW_DATA, $blIv);
		file_put_contents($sTmpPath, $sOriginalPlaintext);
		
		return $sTmpPath;
	}
}
