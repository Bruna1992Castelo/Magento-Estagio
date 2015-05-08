<?php
 
class Query_SalesRecovery_Adminhtml_SalesRecoveryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        if(Mage::helper('Query_SalesRecovery')->getConfig('active'))
        {
            try
            {
                $helper = Mage::helper('Query_SalesRecovery');
                $model = Mage::getModel('Query_SalesRecovery/SalesRecovery')->salesSearch();

                if($model == true)
                {
                    $this->_redirect('*/*/');
                    Mage::getSingleton('core/session')->addSuccess($helper->__('Email sent successfully')); 
                }
                else
                {
                    $this->_redirect('*/*/');
                    Mage::getSingleton('adminhtml/session')->addWarning($helper->__('None email sent'));
                }               
            }
            catch(Exception $e)
            {
                Mage::getSingleton('core/session')->addError($helper->__('Error: email not sent'));   
                $this->_redirect('*/*/');        
            }
        }
        else
        {
            Mage::getSingleton('core/session')->addError($helper->__('Module is not active'));
            $this->_redirect('*/*/');        
        }
    }

    public function exportSalesCsvAction()
    {
        $fileName = 'Query_SalesRecovery.csv';
        $grid = $this->getLayout()->createBlock('Query_SalesRecovery/adminhtml_salesrecovery');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
 
    public function exportSalesExcelAction()
    {
        $fileName = 'Query_SalesRecovery.xml';
        $grid = $this->getLayout()->createBlock('Query_SalesRecovery/adminhtml_salesrecovery');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
   
}