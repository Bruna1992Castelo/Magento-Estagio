<?php
 
class Query_SalesRecovery_Model_Mysql4_SalesRecovery extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('Query_SalesRecovery/SalesRecovery','ranking_id');
    }
}