<?php
 
class Query_SalesRecovery_Block_Adminhtml_SalesRecovery_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesrecovery_grid');
        $this->setDefaultSort('number_of_orders');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
	{
		$collection = Mage::getModel('Query_SalesRecovery/SalesRecovery')->getCollection();
		$collection->setOrder('number_of_orders', 'DESC');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

    protected function _prepareColumns()
    {
        $this->addColumn('ranking_id', array(
            'header' => Mage::helper('Query_SalesRecovery')->__('#'),
            'index'  => 'ranking_id'
        ));

        $this->addColumn('customer_name', array(
            'header' => Mage::helper('Query_SalesRecovery')->__('Name'),
            'index'  => 'customer_name'
        ));

        $this->addColumn('number_of_orders', array(
            'header' => Mage::helper('Query_SalesRecovery')->__('Order quantity'),
            'index'  => 'number_of_orders'
        ));

        $this->addColumn('last_order_date', array(
            'header' => Mage::helper('Query_SalesRecovery')->__('Last order date'),
            'index'  => 'last_order_date'
        ));

        $this->addColumn('last_promotional_email_date', array(
            'header' => Mage::helper('Query_SalesRecovery')->__('Last promotional email date'),
            'index'  => 'last_promotional_email_date'
        ));

        $this->addColumn('total_value_spent', array(
            'header'        => Mage::helper('Query_SalesRecovery')->__('Total value spent'),
            'index'         => 'total_value_spent',
            'type'          => 'currency',
            'currency_code' => $currency
        ));

        $helper=Mage::helper('Query_SalesRecovery');
        $this->addExportType('*/*/exportSalesCsv', $helper->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', $helper->__('Excel XML'));
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}
