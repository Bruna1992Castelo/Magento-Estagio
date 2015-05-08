<?php
 
class Query_SalesRecovery_Block_Adminhtml_SalesRecovery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'Query_SalesRecovery';
        $this->_controller = 'adminhtml_salesRecovery';
        $this->_headerText = Mage::helper('Query_SalesRecovery')->__('Sales Recovery');
        $this->_removeButton('add');
        $this->_addButton('Send Email', array(
            'label'   => Mage::helper('Query_SalesRecovery')->__('Send Email'),
            'onclick' => "setLocation('{$this->getUrl('*/*/grid')}')",
            'class'   => 'button'
        ));
    }
}

 


