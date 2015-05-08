
<?php

class Query_SalesRecovery_Helper_Data extends Mage_Core_Helper_Abstract 
{
    public function getConfig($config)
    {
        return Mage::getStoreConfig("salesrecovery/settings/".$config);
    }
   
 }

