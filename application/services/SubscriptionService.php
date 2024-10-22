<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Subscription Service Class
 *
 * @access public
 */
class SubscriptionService extends Service
{
	///////////////////////////////////////////////////////////////////////////
	///// Properties   ///////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Model Class Name
	 *
	 * @access protected
	 */
	protected $sModelClass = 'SubscriptionModel';

	///////////////////////////////////////////////////////////////////////////
	/////  Class Methods   ///////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Price
	 *
	 * @access protected
	 * @param SubscriptionModel $oSub
	 * @return float
	 */
	protected function _getPrice(SubscriptionModel $oSub) {
		// Support for new plans
		$iPrice = 0;

		if ($oSub->type > SubscriptionModel::TypeFree) {
			$aPrices = get_instance()->config->item('plans');
			$iPrice = $aPrices[$oSub->type]['price'];
		}

		//$iPrice = $this->_applyCoupon($oSub->member_id,$iPrice);
		return round($iPrice,2);
	}

	protected function _applyCoupon($iMemberId,$iPrice) {
		$oCoupon = $this->getCoupons(array('member_id'=>$iMemberId))->first();

		if (empty($oCoupon)) {
			return $iPrice;
		}

		return $this->applyCouponDiscount($iPrice,$oCoupon);
	}

	public function applyCouponDiscount($iPrice,$oCoupon) {
		$aCoupons = get_instance()->config->item('coupons');
		if (!isset($aCoupons[$oCoupon->coupon])) {
			return $iPrice;
		}

		$aCoupon = $aCoupons[$oCoupon->coupon];

		switch ($aCoupon['type']) {
			case 'percent':
				$iPrice *= (100 - $aCoupon['amount']) / 100;
				break;
			case 'fixed':
				$iPrice -= $aCoupon['amount'];
				break;
		}

		return $iPrice;
	}

	/**
	 * Is Type of Subscription
	 *
	 * @access protected
	 * @param integer $iType
	 * @return boolean
	 */
	protected function _isType($iType) {
		$oModel = new SubscriptionModel();
		return $oModel->hasType($iType);
	}

	/**
	 * Setup Default Fields
	 *
	 * @access protected
	 * @param SubscriptionModel $oSub
	 * @return SubscriptionModel
	 */
	protected function _setupDefaultSubscriptionFields(SubscriptionModel $oSub) {
		$aDefaultOptions = get_instance()->config->item('default_subscription_fields');
		foreach ($aDefaultOptions as $sField=>$mValue) {
			if (!$oSub->hasField($sField)) {
				$oSub->$sField = $mValue;
			}
		}

		if (!$oSub->start_date) {
			$oSub->start_date = date('Y-m-d H:i:s');
		}

		if ($oSub->type == SubscriptionModel::TypeFree && !$oSub->expire_date) {
			$oSub->expire_date = date('Y-m-d H:i:s',strtotime('+1 month'));
		}

		$oSub->subscription_name = 'SEO Alarms Subscription';
		$oSub->price = $this->_getPrice($oSub);

		return $oSub;
	}

	///////////////////////////////////////////////////////////////////////////
	/////  Main Methods   ////////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Get Price
	 *
	 * @access public
	 * @param SubscriptionModel $oSub
	 * @return ServiceResponse
	 */
	public function getPrice(SubscriptionModel $oSub) {
		$oSub->price = $this->_getPrice($oSub);

		return new ServiceResponse(array($oSub));
	}


	///////////////////////////////////////////////////////////////////////////
	/////  Subscriptions Methods   ///////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	public function getSubscriptionCount($aFilters) {
		$iCount = $this->_getModel('subscriptions_m')->countItems($aFilters);

		$oResponse = new ServiceResponse();
		$oResponse->total_count = $iCount;

		return $oResponse;
	}

	/**
	 * Add Subscription
	 *
	 * @access public
	 * @param SubscriptionModel $oSubscription
	 * @return ServiceResponse
	 */
	public function addSubscription(SubscriptionModel $oSubscription) {
		$oSubscription->create_date = date('Y-m-d H:i:s');
		$oSubscription->last_changed = date('Y-m-d H:i:s');

		$iResult = $this->_getModel('subscriptions_m')->addItem($oSubscription->toArray());
		if ($iResult) {
			$oSubscription->subscription_id = $iResult;
			$oSubscription->isSaved(true);
			return new ServiceResponse(array($oSubscription));
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Get Subscriptions
	 *
	 * @access public
	 * @param array $aFilters
	 * @param string $sOrderBy (Optional, 'subscription_id asc')
	 * @param integer $iLimit (Optional)
	 * @param integer $iOffset (Optional)
	 * @return ServiceResponse
	 */
	public function getSubscriptions($aFilters=array(),$sOrderBy='subscription_id asc',$iLimit=0,$iOffset=0) {
		$aSubscriptions = $this->_getModel('subscriptions_m')->getItems($aFilters,$sOrderBy,$iLimit,$iOffset);

		if (!empty($aSubscriptions)) {
			return $this->_setupResponse($aSubscriptions);
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Has Active Subscription
	 *
	 * @access public
	 * @param integer $iMemberId
	 * @param integer $iType
	 * @return ServiceResponse
	 */
	public function hasActiveSubscription($iMemberId,$iType=null) {
		$aFilters = array(
			'member_id' => $iMemberId
		);

		if (!empty($iType)) {
			$aFilters['type'] = $iType;
		}

		$oSubscriptionsResponse = $this->getSubscriptions($aFilters);

		if (!$oSubscriptionsResponse->count) {
			return $this->_setupErrorResponse();
		}

		$bFound = false;
		foreach ($oSubscriptionsResponse->getResults() as $oSubscription) {
			if ($oSubscription->isActive()) {
				$bFound = true;
			}
		}

		if ($bFound) {
			return new ServiceResponse();
		}

		return $this->_setupErrorResponse();
	}

	/**
	 * Update Subscription
	 *
	 * @access public
	 * @param SubscriptionModel $oSubscription
	 * @return ServiceResponse
	 */
	public function updateSubscription(SubscriptionModel $oSubscription) {
		$oSubscription->last_changed = date('Y-m-d H:i:s');

		$bUpdated = $this->_getModel('subscriptions_m')->updateItem($oSubscription->toArray());

		if ($bUpdated) {
			$oSubscription->isSaved(true);
			return new ServiceResponse(array($oSubscription));
		}

		return $this->_setupErrorResponse();
	}

	///////////////////////////////////////////////////////////////////////////
	/////  Coupons Methods   /////////////////////////////////////////////////
	/////////////////////////////////////////////////////////////////////////

	/**
	 * Add Coupon
	 *
	 * @access public
	 * @param CouponModel $oCoupon
	 * @return ServiceResponse
	 */
	public function addCoupon(CouponModel $oCoupon) {
		$iResult = $this->_getModel('coupons_m')->addItem($oCoupon->toArray());
		if ($iResult) {
			$oCoupon->coupon_id = $iResult;
			$oCoupon->isSaved(true);
			return new ServiceResponse(array($oCoupon));
		}

		return $this->_setupErrorResponse();
	}

	public function getCoupons($aFilters) {
		$aCoupons = $this->_getModel('coupons_m')->getItems($aFilters);

		if (!empty($aCoupons)) {
			return $this->_setupResponse($aCoupons,'CouponModel');
		}

		return $this->_setupErrorResponse();
	}
}
