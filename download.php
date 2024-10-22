<?php
error_reporting(0);
ini_set('display_errors', 0);

date_default_timezone_set('America/New_York');
$sDate = date('Y-m-d H:i:s');
$sLogfile = __DIR__ . '/ctcssa/log.txt';
define('CONTRACT_STORAGE', dirname(__FILE__).'/ctcssa/');

session_name('sascn');
@session_start();

$sFilename = !empty($_GET['file'])?$_GET['file']:'none provided';
$iMemberId = !empty($_SESSION['member_id'])?$_SESSION['member_id']:'not found';
$sRemoteAddr = !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'not found';

if (empty($_GET['file'])) {
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} empty file\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

if (!preg_match('/^[a-zA-Z0-9]{32}$/',$sFilename)) {
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} invalid filename '{$sFilename}'\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

if (empty($_SESSION['member_id'])) {
	@file_put_contents($sLogfile,"{$sDate}: {$sRemoteAddr} member_id missing\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

if (empty($_SESSION['member_parent_id'])) {
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} parent_id missing\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}
$iParentId = $_SESSION['member_parent_id'];

// check db
$oDB = new mysqli("localhost", "cmadmin", "2Aaap2HftTXOzqmvz3anw4i4Dt9w0I4d", "cmadmin");
if (empty($oDB)) {
	@file_put_contents($sLogfile,"{$sDate}: can not connect to db\n",FILE_APPEND);
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	exit;
}

$sQuery = "SELECT * FROM `contract_revisions` WHERE `parent_id` = \"{$iParentId}\" AND `file_hash` = \"{$sFilename}\" ORDER BY `contract_revision_id` DESC LIMIT 1";
$oResult = $oDB->query($sQuery);
if (empty($oResult)) {
	$oDB->close();
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} {$sFilename}contract not found for file\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

$aContract = $oResult->fetch_assoc();
if (empty($aContract)) {
	$oResult->free();
	$oDB->close();
	//@file_put_contents('/var/www/html/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$iParentId}/{$sRemoteAddr}/{$sFilename} Query: {$sQuery}\n",FILE_APPEND);
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$iParentId}/{$sRemoteAddr} contract not found in db {$sFilename}\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}
$oResult->free();

if ($aContract['owner_id'] != $iMemberId) {
	$sQuery = "SELECT * FROM `contract_members` WHERE `contract_id` = \"{$aContract['contract_id']}\" AND `member_id` = \"{$iMemberId}\" LIMIT 1";
	$oResult = $oDB->query($sQuery);
	if (empty($oResult)) {
		$oDB->close();

		@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} no access for member for {$aContract['contract_id']}\n",FILE_APPEND);
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	$oResult->free();
}
$oDB->close();

$sFullPath = CONTRACT_STORAGE.substr($sFilename,0,1).'/'.substr($sFilename,1,1).'/'.substr($sFilename,2,1).'/'.$sFilename;
if (!file_exists($sFullPath)) {
	@file_put_contents($sLogfile,"{$sDate}: {$iMemberId}/{$sRemoteAddr} file not found on drive {$sFullPath}\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

$bDestroyAlt = false;
if (!empty($aContract['enct'])) {
	if ($aContract['enct'] == '1') {
		require_once(__DIR__.'/vendor/autoload.php');
		$aOptions = array(
			'adapter'   => 'BlockCipher',
			'vector'    => md5($aContract['file_hash'].md5('h53bhq35hq34&^(&R%4hhb'.$aContract['contract_id'])),
			'algorithm' => 'rijndael-192',
			'key'       => md5('superRoper%&#@$72345'.$aContract['file_hash'].md5($aContract['contract_id']))
		);
		$oEncrypt = new Zend\Filter\File\Decrypt($aOptions);
		$sTmpPath = '/tmp/'.$iMemberId.'_'.$aContract['file_hash'];
		touch($sTmpPath);
		@chmod($sTmpPath,0777);
		$oEncrypt->setFilename($sTmpPath);
		$oEncrypt->filter($sFullPath);
		$sFullPath = $sTmpPath;
		$bDestroyAlt = true;
		//@file_put_contents('/var/www/html/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} file encrypted {$aContract['contract_id']}\n",FILE_APPEND);
	} else {
		$sTmpPath = '/tmp/'.$iMemberId.'_'.$aContract['file_hash'];
		touch($sTmpPath);
		@chmod($sTmpPath,0777);
		$sEncryptedContent = file_get_contents($sFullPath);
		$sDecodedContent = openssl_decrypt(
			$sEncryptedContent, $aContract['cipher'], $aContract['cipher_key']
			,0, $aContract['cipher_iv'], $aContract['cipher_tag']);
		@file_put_contents($sTmpPath, $sDecodedContent);
		$sFullPath = $sTmpPath;
		$bDestroyAlt = true;
	}
} else {
	//@file_put_contents('/var/www/html/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} file not encrypted {$aContract['contract_id']}\n",FILE_APPEND);
}

$fSize = filesize($sFullPath);

header("Content-type: application/octet-stream");
header("Content-Disposition: filename=\"".$aContract['file_name']."\"");
header("Content-length: {$fSize}");
header("Cache-control: private"); //use this to open files directly

$oFile = fopen($sFullPath,'r');
fpassthru($oFile);
fclose($oFile);
if (!empty($bDestroyAlt)) {
	@unlink($sFullPath);
}
exit;

