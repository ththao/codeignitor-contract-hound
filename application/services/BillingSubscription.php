<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 /**
  * Billing Subcription Via Authorize.net ARB
  *
  * @access public
  */
class BillingSubscription {

	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * CodeIgniter Instance
	 *
	 * @access protected
	 */
	protected $_ci;

	/**
	 * Authorize.net Login
	 *
	 * @access protected
	 */
	protected $loginname;

	/**
	 * Authorize.net Transaction Key
	 *
	 * @access protected
	 */
	protected $transactionkey;

	/**
	 * Authorize.net API Url
	 *
	 * @access protected
	 */
	protected $host;

	/**
	 * Status Options
	 *
	 * @access public
	 */
	const StatusActive     = 1;
	const StatusExpired    = 2;
	const StatusSuspended  = 3;
	const StatusCanceled   = 4;
	const StatusTerminated = 5;

	/**
	 * Status Options Translation
	 *
	 * @access protected
	 */
	protected $aStatusOptions = array(
		 self::StatusActive     => 'active'
		,self::StatusExpired    => 'expired'
		,self::StatusSuspended  => 'suspended'
		,self::StatusCanceled   => 'canceled'
		,self::StatusTerminated => 'terminated'
	);

	///////////////////////////////////////////////////////////////////////////
	/////  Super Methods   ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->_ci =& get_instance();
		$this->_ci->config->load('billing');

		$this->loginname	  = $this->_ci->config->item('loginname');
		$this->transactionkey = $this->_ci->config->item('transactionkey');
		$this->host		      = $this->_ci->config->item('host');
		$this->path		      = $this->_ci->config->item('path');
	}

	///////////////////////////////////////////////////////////////////////////
	/////  General Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Create Subscription
	 *
	 * @access public
	 * @param array $data
	 *     - ref_id
	 *     - name
	 *     - length (9999 months)
	 *     - unit (1 month)
	 *     - start_date
	 *     - total_occurrences
	 *     - trial_occurrences (0)
	 *     - amount
	 *     - trial_amount (0)
	 *     - card_number
	 *     - expiration_date
	 *     - first_name
	 *     - last_name
	 * @return array
	 */
	public function createSubscription($data) {
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>" .
			"<ARBCreateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">" .
				"<merchantAuthentication>".
					"<name>" . $this->loginname . "</name>".
					"<transactionKey>" . $this->transactionkey . "</transactionKey>".
				"</merchantAuthentication>".
				//"<refId>" . $data["ref_id"] . "</refId>".  // not needed
				"<subscription>".
					"<name>" . $data["subscription_name"] . "</name>".
					"<paymentSchedule>".
						"<interval>".
							"<length>". $data["length"] ."</length>".
							"<unit>". $data["unit"] ."</unit>".
						"</interval>".
						"<startDate>" . date('Y-m-d',strtotime($data["start_date"])) . "</startDate>".
						"<totalOccurrences>". $data["total_occurrences"] . "</totalOccurrences>".
						"<trialOccurrences>". $data["trial_occurrences"] . "</trialOccurrences>".
					"</paymentSchedule>".
					"<amount>". $data["price"] ."</amount>".
					"<trialAmount>0.00</trialAmount>".
					"<payment>".
						"<creditCard>".
							"<cardNumber>" . $data["cc_number"] . "</cardNumber>".
							"<expirationDate>20" . $data["cc_expire_year"].'-'.$data["cc_expire_month"] . "</expirationDate>".
							"<cardCode>" . $data["cvv"] . "</cardCode>".
						"</creditCard>".
					"</payment>".
					"<billTo>".
						"<firstName>". $data["first_name"] . "</firstName>".
						"<lastName>" . $data["last_name"] . "</lastName>".
						"<address>" . $data["address"] . "</address>".
						"<city>" . $data["city"] . "</city>".
						"<state>" . $data["state"] . "</state>".
						"<zip>" . $data["zip"] . "</zip>".
					"</billTo>".
				"</subscription>".
			"</ARBCreateSubscriptionRequest>";

		//send the xml via curl
		$response = $this->send_request_via_curl($this->host,$this->path,$content);
		log_message('required','arb create response: '.print_r($response,true));

		//if the connection and send worked $response holds the return from Authorize.net
		if ($response) {
			list ($refId, $resultCode, $code, $text, $subscriptionId) = $this->parse_return($response);

			if ($resultCode == "Ok") {
				return array(
					 'success'         => TRUE
					,'subscription_id' => $subscriptionId
				);
			} else {
				//TODO: needs to return specific errors!
				return array(
					'success' => FALSE
				);
			}
		} else {
			return array(
				'success' => FALSE
			);
		}
	}

	/**
	 * check subscription
	 *
	 * @access public
	 * @param array $data
	 *   - subscription_di
	 * @return array
	 *   - success (boolean)
	 *   - status (string)
	 */
	public function checkSubscription($data) {
		$content =
			"<?xml version=\"1.0\"?>".
			"<ARBGetSubscriptionStatusRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
				"<merchantAuthentication>".
					"<name>".$this->loginname."</name>".
					"<transactionKey>".$this->transactionkey."</transactionKey>".
				"</merchantAuthentication>".
				//"<refId>".$data['ref_id']."</refId>".  // not needed
				"<subscriptionId>".$data['subscription_id']."</subscriptionId>".
			"</ARBGetSubscriptionStatusRequest>";

		//send the xml via curl
		$response = $this->send_request_via_curl($this->host,$this->path,$content);
		log_message('required','arb check response: '.print_r($response,true));

		if ($response) {
			$sStatus = $this->substring_between($response,'<status>','</status>');
			$iStatus = $this->_translateStatus($sStatus);

			return array(
				 'success'         => true
				,'status'          => $iStatus
				,'status_readable' => $sStatus
			);
		}

		return array(
			 'success' => false
			,'error'   => 'no response from authorize.net'
		);
	}

	/**
	 * Update Subscription
	 *
	 * @access public
	 * @param array $data
	 *     - subscription_id
	 *     - amount
	 *     - card_number
	 *     - expiration_date
	 * @return boolean
	 */
	public function updateSubscription($data) {
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
			"<ARBUpdateSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
			"<merchantAuthentication>".
				"<name>" . $this->loginname . "</name>".
				"<transactionKey>" . $this->transactionkey . "</transactionKey>".
			"</merchantAuthentication>".
			"<subscriptionId>" . $data["subscription_id"] . "</subscriptionId>".
			"<subscription>".
				"<amount>". $data['subscription_price'] ."</amount>";
				//"<name>". $data['subscription_name'] ."</name>"; // not valid

		if(isset($data["card_number"])) {
			$content .=
				"<payment>".
					"<creditCard>".
						"<cardNumber>" . $data["card_number"] ."</cardNumber>".
						"<expirationDate>20" . $data["cc_expire_year"].'-'.$data["cc_expire_month"] . "</expirationDate>".
						"<cardCode>" . $data["cvv"] . "</cardCode>".
					"</creditCard>".
				"</payment>";
		}

		$content .=
			"</subscription>".
		"</ARBUpdateSubscriptionRequest>";

		//send the xml via curl
		$response = $this->send_request_via_curl($this->host,$this->path,$content);
		log_message('required','arb update response: '.print_r($response,true));

		//if the connection and send worked $response holds the return from Authorize.net
		if ($response) {
			list ($resultCode, $code, $text, $data["subscription_id"]) = $this->parse_return($response);

			if($code == "Ok"){
				return TRUE;
			} else {
				return FALSE;
			}

		//	echo " Response Code: $resultCode <br>";
		//	echo " Response Reason Code: $code<br>";
		//	echo " Response Text: $text<br>";
		//	echo " Subscription Id: $subscriptionId <br><br>";
		//} else {
		//	echo "Transaction Failed. <br>";
		} else {
			return false;
		}
	}

	/**
	 * Cancel Subscription
	 *
	 * @access public
	 * @param array $data
	 *     - subscription_id
	 * @return boolean
	 */
	public function cancelSubscription($data) {
		$content =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
			"<ARBCancelSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
				"<merchantAuthentication>".
					"<name>" . $this->loginname . "</name>".
					"<transactionKey>" . $this->transactionkey . "</transactionKey>".
				"</merchantAuthentication>" .
				"<subscriptionId>" . $data["subscription_id"] . "</subscriptionId>".
			"</ARBCancelSubscriptionRequest>";

		//send the xml via curl
		$response = $this->send_request_via_curl($this->host,$this->path,$content);
		log_message('required','arb cancel response: '.print_r($response,true));

		//if the connection and send worked $response holds the return from Authorize.net
		if ($response) {
			list ($resultCode, $code, $text, $data["subscription_id"]) = $this->parse_return($response);

			if ($code == "Ok") {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
			//echo "Transaction Failed. <br>";
		}

	}

	///////////////////////////////////////////////////////////////////////////
	/////  Support Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Translate Status
	 *
	 * @access protected
	 * @param mixed $mStatus
	 * @return mixed
	 */
	protected function _translateStatus($mStatus) {
		$mReturn = null;

		if (is_numeric($mStatus)) {
			if (isset($this->aStatusOptions[$mStatus])) {
				$mReturn = $this->aStatusOptions[$mStatus];
			}
		} else {
			$mStatus = strtolower(trim($mStatus));
			foreach ($this->aStatusOptions as $iIndex=>$sValue) {
				if (strcmp($mStatus,$sValue) === 0) {
					$mReturn = $iIndex;
				}
			}
		}

		return $mReturn;
	}

	///////////////////////////////////////////////////////////////////////////
	/////  Authorize.net Methods   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	//function to send xml request via curl
	protected function send_request_via_curl($host,$path,$content) {
		$posturl = "https://" . $host . $path;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $posturl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);

		return $response;
	}

	//function to parse Authorize.net response
	protected function parse_return($content) {
		$refId = $this->substring_between($content,'<refId>','</refId>');
		$resultCode = $this->substring_between($content,'<resultCode>','</resultCode>');
		$code = $this->substring_between($content,'<code>','</code>');
		$text = $this->substring_between($content,'<text>','</text>');
		$subscriptionId = $this->substring_between($content,'<subscriptionId>','</subscriptionId>');

		return array($refId, $resultCode, $code, $text, $subscriptionId);
	}

	//helper function for parsing response
	protected function substring_between($haystack,$start,$end) {
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false)	{
			return false;
		} else {
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
}
