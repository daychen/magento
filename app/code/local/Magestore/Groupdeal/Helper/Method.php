<?php
class Magestore_Groupdeal_Helper_Method extends Mage_Payment_Helper_Data{
	 public function getStoreMethods($store = null, $quote = null)
    {
        $res = array();
        foreach ($this->getPaymentMethods($store) as $code => $methodConfig) {
            $prefix = self::XML_PATH_PAYMENT_METHODS . '/' . $code . '/';
            if (!$model = Mage::getStoreConfig($prefix . 'model', $store)) {
                continue;
            }
            $methodInstance = Mage::getModel($model);
            if (!$methodInstance) {
                continue;
            }
            $methodInstance->setStore($store);
            if (!$methodInstance->isAvailable($quote)) {
                /* if the payment method cannot be used at this time */
                continue;
            }
            $sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
            $methodInstance->setSortOrder($sortOrder);
            $res[] = $methodInstance;
        }
		
		//check item in cart, if item is groupdeal product, method is paypal standard
		if($quote){
			$allItems = $quote->getAllItems();
			foreach($allItems as $item){
				$groupdealProductId = Mage::helper('groupdeal')->getGroupdealProductIdFromItem($item);
				if($groupdealProductId){
					/* $deal = Mage::getModel('groupdeal/deal')->loadDealByProduct($item);
					if($deal->getBought() >= $deal->getMinimumPurchase()){
						break;
					} */
					$methodInstance = Mage::getModel('paypal/standard');
					$sortOrder = (int)$methodInstance->getConfigData('sort_order', $store);
					$methodInstance->setSortOrder($sortOrder);
					$methods[] = $methodInstance;
					usort($methods, array($this, '_sortMethods'));
					return $methods;
				}
			}
		}
		
        usort($res, array($this, '_sortMethods'));
        return $res;
    }
}