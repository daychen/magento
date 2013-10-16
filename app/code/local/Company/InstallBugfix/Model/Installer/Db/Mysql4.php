<?php
  class Company_InstallBugfix_Model_Installer_Db_Mysql4 extends Mage_Install_Model_Installer_Db_Mysql4{
	
	public function supportEngine(){
		$supportsEngine=parent::supportEngine();
		if($supportsEngine){
		   return true;
		}
		$variables=$this->_getConnection()->fetchPairs('SHOW ENGINES');
		
		return (isset($variables['InnoDB']) && $variables['InnoDB']!='NO');

	}	
}



>
