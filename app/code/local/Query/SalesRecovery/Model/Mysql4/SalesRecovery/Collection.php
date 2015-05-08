<?php
 
class Query_SalesRecovery_Model_Mysql4_SalesRecovery_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Query_SalesRecovery/SalesRecovery');
    }
}