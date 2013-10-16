<?php
class Magestore_Groupdeal_IndexController extends Mage_Core_Controller_Front_Action {	
 
	
	public function indexAction(){

		//auto set status
		$categoryId = $this->getRequest()->getParam('category');
		
		$collection = Mage::getModel('groupdeal/deal')->getCollection()
					->addFieldToFilter('deal_status', array('in'=> array(6,5,4))); //deal is active, will active
		
		foreach($collection as $item){
			$item->setStatus();
		}
		
		$deals = Mage::helper('groupdeal')->getSendMailUnreachedDeals();
		foreach($deals as $deal){
			Mage::helper('groupdeal')->sendCancelDealEmailToCustomers($deal);
		}
		
		//if only one deal, redirect to this deal
		$dealProductIds = Mage::helper('groupdeal')->getActiveGroupdealProductIds($categoryId);
		if(count($dealProductIds) == 1){
			$deal = Mage::getModel('groupdeal/deal')->loadDealByProduct($dealProductIds[0]);
			$this->_redirectUrl($deal->getDealUrl());
			return;
		}
		
		$this->loadLayout();     
		$this->renderLayout();
    }
	
	public function dealAction(){

		$dealId = $this->getRequest()->getParam('id');
		$deal = Mage::getModel('groupdeal/deal')->load($dealId);
		$deal = $deal->setStatus();
		
		if($deal->getDealStatus() == 3 && $deal->getIsSendmailUnreached() == 0){ //deal is unreached and not send mail
			Mage::helper('groupdeal')->sendCancelDealEmailToCustomers($deal);//send email off deal
		}
		
		$this->loadLayout();     
		$this->renderLayout();
	}
	
	public function subscribeAction(){
		
		$this->loadLayout();     
		$this->renderLayout();
	}
	
	public function newsletterAction(){
		try{
			if($this->getRequest()->getPost()){
				$data = $this->getRequest()->getPost();
			
				if($data['category_ids'])
					$data['categories'] = implode(',', $data['category_ids']);
				else
					$data['categories'] = 0;
				
				if(isset($data['unsubscribe']) && $data['unsubscribe'] == 1)
					$data['status'] = 0;
				else
					$data['status'] = 1;
				
				//print_r($data);die();
				$subscriberId = Mage::helper('groupdeal')->getSubscriberId($data['email']);//get Subscribe Id if subscribe exist
				
				$subscriber = Mage::getModel('groupdeal/subscriber')
							->setData($data);
				if($subscriberId)
					$subscriber->setId($subscriberId);
				
				$subscriber->save();
				
				if($subscriber->getStatus() == 1)
					Mage::getSingleton('core/session')->addSuccess($this->__('Subscribed successfull'));
				else
					Mage::getSingleton('core/session')->addSuccess($this->__('Unsubscribed successfull'));
			}
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError($this->__('Error, please try again'));
		}
		
		$returnUrl = Mage::getSingleton('core/session')->getNewsleterUrl();
		$this->_redirectUrl($returnUrl);
	}
	
	public function whatHappenToYourPurchaseAction(){
		$this->loadLayout();     
		$this->renderLayout();
	}
	
}