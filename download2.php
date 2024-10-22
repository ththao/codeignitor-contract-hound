<?php

error_reporting(-1);
ini_set('display_errors', 1);

date_default_timezone_set('America/New_York');
$sDate = date('Y-m-d H:i:s');

session_name('sascn');
@session_start();

require_once ('vendor/autoload.php');
require_once ('application/libraries/Segment.php');

use Aws\S3\S3Client;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ( $_SERVER['CI_ENV'] == 'development')
{
  putenv("HOME=/Users/nicholasholmes"); // enter path to local AWS .credentials file here
}

$sFilename = !empty($_GET['file'])?$_GET['file']:'none provided';
$iMemberId = !empty($_SESSION['member_id'])?$_SESSION['member_id']:'not found';
$sRemoteAddr = !empty($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'not found';

if (empty($_GET['file'])) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} empty file\n",FILE_APPEND);
	header('HTTP/1.0 400 Bad Request');
	exit;
}

if (!preg_match('/^[a-zA-Z0-9]{32}$/',$sFilename)) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} invalid filename '{$sFilename}'\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

if (empty($_SESSION['member_id'])) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$sRemoteAddr} member_id missing\n",FILE_APPEND);
	header('HTTP/1.0 401 Unauthorized');
	exit;
}

if (empty($_SESSION['member_parent_id'])) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} parent_id missing\n",FILE_APPEND);
	header('HTTP/1.0 401 Unauthorized');
	exit;
}
$iParentId = $_SESSION['member_parent_id'];

// check db
$oDB = new mysqli($_ENV['DB_HOSTNAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);
if (empty($oDB)) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: can not connect to db\n",FILE_APPEND);
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	exit;
}
$sQuery = "SELECT * FROM `contract_support_docs` WHERE `parent_id` = \"{$iParentId}\" AND `file_hash` = \"{$sFilename}\" LIMIT 1";
$oResult = $oDB->query($sQuery);
if (empty($oResult) || $oResult->num_rows == 0) {
	$sQuery = "SELECT * FROM `contract_revisions` WHERE `parent_id` = \"{$iParentId}\" AND `file_hash` = \"{$sFilename}\" ORDER BY `contract_revision_id` DESC LIMIT 1";
} else {
	$aContractSupportingDoc = $oResult->fetch_assoc();
	$oResult->free();

	$sQuery = "SELECT * FROM `contract_revisions` WHERE `parent_id` = \"{$iParentId}\" AND `contract_id` = \"{$aContractSupportingDoc['contract_id']}\" ORDER BY `contract_revision_id` DESC LIMIT 1";
}

$oResult = $oDB->query($sQuery);
if (empty($oResult) || $oResult->num_rows == 0) {
	$oDB->close();
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} {$sFilename} contract not found for file\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

$aContract = $oResult->fetch_assoc();
if (empty($aContract)) {
	$oResult->free();
	$oDB->close();
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$iParentId}/{$sRemoteAddr}/{$sFilename} Query: {$sQuery}\n",FILE_APPEND);
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$iParentId}/{$sRemoteAddr} contract not found in db {$sFilename}\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}
$oResult->free();

if ($aContract['owner_id'] != $iMemberId) {
	$sQuery = "SELECT * FROM `contract_members` WHERE `contract_id` = \"{$aContract['contract_id']}\" AND `member_id` = \"{$iMemberId}\" LIMIT 1";
	$oResult = $oDB->query($sQuery);
	if (empty($oResult)) {
		$oDB->close();

		@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} no access for member for {$aContract['contract_id']}\n",FILE_APPEND);
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	$oResult->free();
}
$oDB->close();

$oAWSClient = S3Client::factory(array(
	'profile'  => $_ENV['S3_BUCKET']
	,'region'  => 'us-east-1'
	,'version' => '2006-03-01'
));

$sFullPath = substr($sFilename,0,1).'/'.substr($sFilename,1,1).'/'.substr($sFilename,2,1).'/'.$sFilename;
$bResult = $oAWSClient->doesObjectExist($_ENV['S3_BUCKET'],$sFullPath);
if (!$bResult) {
	@file_put_contents('/jet/app/www/default/ctcssa/log.txt',"{$sDate}: {$iMemberId}/{$sRemoteAddr} file not found on drive {$sFullPath}\n",FILE_APPEND);
	header('HTTP/1.0 404 Not Found');
	exit;
}

$sTmpPath = '/tmp/'.$iMemberId.'_'.$sFilename;

$bResult = $oAWSClient->getObject(array(
	'Bucket' => $_ENV['S3_BUCKET'],
	'Key'    => $sFullPath,
	'SaveAs' => $sTmpPath
));

$iKey = $aContract['contract_id'];
$sHash = $aContract['file_hash'];
$iIvLen = $aContract['ivlen'];
$blIv = $aContract['iv'];
$sFileName = $aContract['file_name'];
if (!empty($aContractSupportingDoc)) {
	$iKey = $aContractSupportingDoc['contract_support_doc_id'];
	$sHash = $aContractSupportingDoc['file_hash'];
	$iIvLen = $aContractSupportingDoc['ivlen'];
	$blIv = $aContractSupportingDoc['iv'];
	$sFileName = $aContractSupportingDoc['file_name'];
}
$sEncodeKey = md5('superRoper%&#@$72345'.$sHash.md5($iKey));

$sCipherText = file_get_contents($sTmpPath);
$sCipherText = base64_decode($sCipherText);

$sCiphertextRaw = substr($sCipherText, $iIvLen + 32);
$sOriginalPlaintext = openssl_decrypt($sCiphertextRaw, 'AES-128-CBC', $sEncodeKey, OPENSSL_RAW_DATA, $blIv);
file_put_contents($sTmpPath, $sOriginalPlaintext);

$fSize = filesize($sTmpPath);

Segment::init($_ENV['SEGMENT_KEY']);
Segment::track([
    "userId" => $_SESSION['member_id'],
    "event" => 'Contract Downloaded',
    "properties" => [
        "contractId" => $iKey,
        "timestamp" => date('Y-m-d H:i:s')
    ]
]);

Segment::flush();

$file_parts = pathinfo($sFileName);

if (isset($file_parts['extension']) && 'pdf' == $file_parts['extension']) {
    if (isset($_GET['type']) && 'view' == $_GET['type']) {
        header("Content-type: application/pdf");
        header("Content-Disposition", "inline; filename=".$sFileName);
        header("Content-length: {$fSize}");
        header("Cache-control: private"); //use this to open files directly
    } else {
        header("Content-type: application/pdf");
        header("Content-Disposition: attachment; filename=".$sFileName);
        header("Content-length: {$fSize}");
        header("Cache-control: private"); //use this to open files directly
    }
} else {
    header("Content-type: application/octet-stream");
    header("Content-Disposition: filename=\"".$sFileName."\"");
    header("Content-length: {$fSize}");
    header("Cache-control: private"); //use this to open files directly
}

$oFile = fopen($sTmpPath,'r');
fpassthru($oFile);

@unlink($sTmpPath);
exit;

