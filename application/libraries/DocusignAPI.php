<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * DocusignAPI
 *
 * @access public
 */
class DocusignAPI extends Service {

	/**
	 * Get web page from url
	 *
	 * @access public
	 * @param string $sUrl
	 * @param bool $bReturnHeaders (Optional, Default False)
	 * @return string|array
	 */
	public function getUrlContents($sUrl,$bReturnHeaders=false,$sAuth=false) {
		if (!empty($_SESSION['debug'])) {
			log_message('required','Start curl: '.$sUrl);
		}

		$oCurlConnection = $this->_initCurl($sAuth);
		$oCurlConnection = $this->_setUserAgent($oCurlConnection);
		$oCurlConnection = $this->_setTimeout($oCurlConnection,90);
		$aReturn = $this->_executeCurl($oCurlConnection,$sUrl);

		if ($bReturnHeaders) {
			return $aReturn;
		}

		return $aReturn['content'];
	}

	/**
	 * Post Url Contents
	 *
	 * @access public
	 * @param string $sUrl
	 * @param array $aPostData
	 * @param int $iTimeout (Optional, 90)
	 * @return array
	 */
	public function postUrlContents($sUrl,$aPostData=array(),$iTimeout=90,$sAuth=false) {
		$oCurlConnection = $this->_initCurl($sAuth);
		$oCurlConnection = $this->_setUserAgent($oCurlConnection);
		$oCurlConnection = $this->_setTimeout($oCurlConnection,$iTimeout);
		curl_setopt($oCurlConnection, CURLINFO_HEADER_OUT, true);

		if (!empty($aPostData)) {
			curl_setopt($oCurlConnection, CURLOPT_POST, TRUE);

			$sPostData = '';
			foreach ($aPostData as $sKey=>$mValue) {
				if (!empty($sPostData)) {
					$sPostData .= '&';
				}
				$sPostData .= $sKey.'='.urlencode($mValue);
			}
			curl_setopt($oCurlConnection, CURLOPT_POSTFIELDS, $sPostData);
		}

		$aReturn = $this->_executeCurl($oCurlConnection,$sUrl);
		return $aReturn;
	}

	/**
	 * Execute Curl
	 *
	 * @access protected
	 * @param resource $oCurlConnection
	 * @param string $sUrl
	 * @return array
	 */
	protected function _executeCurl($oCurlConnection,$sUrl) {
		curl_setopt($oCurlConnection, CURLOPT_URL,$sUrl); // Url to request

		// run it
		$sContent = curl_exec($oCurlConnection);
		$aHeaderInfo = curl_getinfo($oCurlConnection);
		$aError = curl_error($oCurlConnection);
		curl_close($oCurlConnection);

		// extract body and header
		$iCurlHeaderSize = $aHeaderInfo['header_size'];

		$sBodyHeader = trim(mb_substr($sContent, 0, $iCurlHeaderSize));
		$sContent = trim(mb_substr($sContent, $iCurlHeaderSize));
		//log_message('required',__METHOD__.' headers: '.print_r($sBodyHeader,true));

		$aBodyHeaders = $this->_parseHeaders($sBodyHeader);
		$aHeaderInfo = array_merge($aHeaderInfo,$aBodyHeaders);

		// check for error
		if (empty($aHeaderInfo['http_code']) || $aHeaderInfo['http_code'] != 200) {
			//log_message('error',__METHOD__.' url: '.$sUrl.' header: '.print_r($aHeaderInfo,true));
			//log_message('error',__METHOD__.' url: '.$sUrl.' error: '.print_r($aError,true));
		}

		//send it
		return array(
			'success'  => $aHeaderInfo['http_code'] == '200' ? 1 : 0
			,'header'  => $aHeaderInfo
			,'content' => $sContent
		);
	}

	/**
	 * Setup Timeout
	 *
	 * @access protected
	 * @param resource $oCurlConnection
	 * @param int $iTimeout
	 * @return resource mixed
	 */
	protected function _setTimeout($oCurlConnection,$iTimeout=90) {
		curl_setopt($oCurlConnection, CURLOPT_TIMEOUT, $iTimeout); // Curl Process Timeout
		return $oCurlConnection;
	}

	/**
	 * Set User Agent
	 *
	 * @access protected
	 * @param resource $oCurlConnection
	 * @param bool $bCheckAsFirefox
	 * @return resource
	 */
	protected function _setUserAgent($oCurlConnection,$bCheckAsFirefox=true) {
		curl_setopt($oCurlConnection, CURLOPT_USERAGENT, $this->_getUserAgent($bCheckAsFirefox)); // User Agent of request
		return $oCurlConnection;
	}

	/**
	 * Init Curl Connection
	 *
	 * @access public
	 * @return resource
	 */
	protected function _initCurl($sAuth=false) {
		$oCurlConnection = curl_init();
		curl_setopt($oCurlConnection, CURLOPT_AUTOREFERER, true);                    // If redirected, use previous as referer
		curl_setopt($oCurlConnection, CURLOPT_FAILONERROR, true);                    // If error code found, fail connection
		curl_setopt($oCurlConnection, CURLOPT_RETURNTRANSFER, true);                 // Return response
		curl_setopt($oCurlConnection, CURLOPT_CONNECTTIMEOUT, 30);                   // Time out for a single connection
		curl_setopt($oCurlConnection, CURLOPT_FOLLOWLOCATION, true);                 // Follow Redirects If 302
		curl_setopt($oCurlConnection, CURLOPT_MAXREDIRS, 30);                        // Max Redirects Allowed
		curl_setopt($oCurlConnection, CURLOPT_HTTPHEADER, $this->_getCurlHeaders()); // Headers
		curl_setopt($oCurlConnection, CURLOPT_ENCODING, "deflate");                  // Can deflate response if gzipped
		curl_setopt($oCurlConnection, CURLOPT_HEADER, TRUE);                         // return header in content

		if ($sAuth) {
			curl_setopt($oCurlConnection, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/x-www-form-urlencoded'
				,'Authorization: '.$sAuth
			));
			//curl_setopt($oCurlConnection, CURLOPT_USERPWD, $sBasicAuth);
			//log_message('required','_initCurl basic auth: '.$sAuth);
		}

		return $oCurlConnection;
	}

	/**
	 * Get UserAgent
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getUserAgent() {
		$aUAs = array(
			// mine
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:27.0) Gecko/20100101 Firefox/27.0'
			,'User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36'

			/// from list
				// chrome
			,'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.872.0 Safari/535.2'
			,'Mozilla/5.0 (Windows NT 6.0) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.120 Safari/535.2'
			,'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.93 Safari/537.36'
			,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.60 Safari/537.17'
			,'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1467.0 Safari/537.36'
			,'Mozilla/5.0 (Windows NT 6.2) AppleWebKit/537.13 (KHTML, like Gecko) Chrome/24.0.1290.1 Safari/537.13'
			// firefox
			,'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:25.0) Gecko/20100101 Firefox/25.0'
			,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:25.0) Gecko/20100101 Firefox/25.0'
			,'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0'
			,'Mozilla/5.0 (Windows NT 6.0; WOW64; rv:24.0) Gecko/20100101 Firefox/24.0'
			,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:24.0) Gecko/20100101 Firefox/24.0'
			,'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:21.0) Gecko/20100101 Firefox/21.0'
			,'Mozilla/5.0 (X11; Linux i686; rv:21.0) Gecko/20100101 Firefox/21.0'
			,'Mozilla/5.0 (Windows NT 6.2; rv:21.0) Gecko/20130326 Firefox/21.0'
			// ie
			,'Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)'
			,'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 7.1; Trident/5.0)'
			,'Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727)'
			// opera
			,'Opera/9.80 (Windows NT 6.1; WOW64; U; pt) Presto/2.10.229 Version/11.62'
			,'Opera/9.80 (Windows NT 6.0; U; pl) Presto/2.10.229 Version/11.62'
			,'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52'
			,'Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; de) Presto/2.9.168 Version/11.52'
			// safari
			,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2'
			,'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; de-at) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1'
			,'Mozilla/5.0 (Windows; U; Windows NT 6.1; tr-TR) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27'
			,'Mozilla/5.0 (Windows; U; Windows NT 6.1; sv-SE) AppleWebKit/533.19.4 (KHTML, like Gecko) Version/5.0.3 Safari/533.19.4'
		);

		$iRandIndex = array_rand($aUAs);
		return $aUAs[$iRandIndex];
	}

	/**
	 * Get Curl Headers
	 *
	 * @access protected
	 * @return array
	 */
	protected function _getCurlHeaders() {
		$aHeader = array();
		$aHeader[0] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$aHeader[]  = "Accept-Language: en-us,en;q=0.5";
		$aHeader[]  = "Accept-Encoding: gzip,deflate";
		//$aHeader[]  = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$aHeader[]  = "Connection: keep-alive";
		//$aHeader[]  = "Keep-Alive: 300";
		//$aHeader[]  = "Pragma: "; // browsers keep this blank.
		$aHeader[]  = "Pragma: no-cache";
		$aHeader[]  = "Cache-Control: no-cache"; // browsers keep this blank.
		//$aHeader[]  = "Cache-Control: max-age=0";

		return $aHeader;
	}

	/**
	 * Parse Headers
	 *
	 * @access protected
	 * @param array $sHeader
	 * @return array
	 */
	protected function _parseHeaders($sHeader) {
		$aResponseHeader = explode("\n",$sHeader);
		unset($aResponseHeader[0]);

		$aHeaders = array();
		foreach ($aResponseHeader as $sLine) {
			if (strpos($sLine,':') === false) {
				continue;
			}

			list($sKey,$sVal) = explode(':',$sLine,2);
			$aHeaders[strtolower(trim($sKey))] = trim($sVal);
		}

		return $aHeaders;
	}


	/**
	 * Clean Host
	 *
	 * @static
	 * @param string $sHost
	 * @return string
	 */
	public static function cleanHost($sHost) {
		// handle empty request
		if (empty($sHost)) {
			return false;
		}

		// Clean spacing
		$sHost = trim($sHost);

		// Clean semi-colons, because stupid people exist
		$sHost = trim($sHost,';');

		// Clean trailing slashes
		$sHost = trim($sHost,'/');

		// Case insensitive
		$sHost = strtolower($sHost);

		// Partials (javascript secure/insecure hack)
		if (strpos($sHost,'://') === 0) {
			$sHost = 'http'.$sHost;
		}

		// Make sure it starts with http:// or https://
		if (strpos($sHost,'http://') !== 0 && strpos($sHost,'https://') !== 0) {
			$sHost = 'http://'.$sHost;
		}

		// Clean www
		$sHost = str_replace('://www.','://',$sHost);

		// just in case of www.www.
		$sHost = str_replace('://www.','://',$sHost);

		$sBaseHost = @parse_url($sHost, PHP_URL_HOST);

		// Clean underscores, because stupid people exist
		$sBaseHost = rtrim($sBaseHost,'_');

		if (empty($sBaseHost)) {
			return false;
		}

		if (function_exists('idn_to_ascii')) {
			$sBaseHost = @idn_to_ascii($sBaseHost);
		}

		// Is Valid
		if (!filter_var('http://'.$sBaseHost, FILTER_VALIDATE_URL)) {
			return false;
		}

		return $sBaseHost;
	}

	public static function getBaseDomain($sUrl) {
		$sUrl = trim($sUrl);
		if (empty($sUrl) || !filter_var($sUrl,FILTER_VALIDATE_URL)) {
			return false;
		}

		$sDomain = self::getDomainHost($sUrl);
		$sTld = self::getDomainTld($sUrl);

		if (strpos($sDomain,'www.')===0) {
			$sDomain = substr($sDomain,4);
		}

		return substr($sDomain,0,strlen($sDomain) - strlen($sTld) - 1);
	}

	/**
	 * get domain host
	 *
	 * @access protected
	 * @param string $url
	 * @return string
	 */
	protected static function getDomainHost($url) {
		$url = strtolower($url);

		if (strstr($url, '://') !== false) {
			$url = @parse_url($url);
			return $url['host']; # PHP5: return parse_url($url, PHP_URL_HOST);
		}

		$parts = explode('/', $url);
		return $parts[0];

	}

	/**
	 * get domain top level domain
	 *
	 * @access protected
	 * @param string $domain
	 * @return string
	 */
	protected static function getDomainTld($domain) {
		$domain = self::getDomainHost($domain);

		$parts = explode('.', $domain);
		$tld   = array_pop($parts);
		$stl   = end($parts);

		if (strlen($tld) < 3 && strlen($stl) <= 3 ) {
			$tld = $stl.'.'.$tld;
		}

		return $tld;
	}
}
