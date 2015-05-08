<?php

class Query_SalesRecovery_Model_SalesRecovery extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
          parent::_construct();
          $this->_init('Query_SalesRecovery/SalesRecovery');
    } 

    public function salesSearch()
    {

        $salesOrders = Mage::getModel('sales/order')->getCollection();  
        $salesOrders->getSelect()->columns('SUM(grand_total) as total, COUNT(*) as total_order')->group('customer_id');

        $date = new Zend_Date();
        
        foreach($salesOrders as $order)
        {
            if(!$order->getCustomerId())
            {
                continue;
            }

            $salesRecovery = Mage::getModel('Query_SalesRecovery/SalesRecovery')->load($order->getCustomerId(),'customer_id');
            
            $data = array(); 
            if($salesRecovery->getRankingId())
            {
            
                $data = array
                (
                    'ranking_id'                    => $salesRecovery->getRankingId(),
                    'customer_id'                   => $order->getCustomerId(),
                    'number_of_orders'              => $order->getData('total_order'), 
                    'total_value_spent'             => $order->getData('grand_total'),
                    'customer_name'                 => $order->getData('customer_firstname').' '.$order->getData('customer_lastname'),
                    'last_order_date'               => $order->getData('created_at')
                ); 
            }
            else
            {

                $data = array
                (
                    'customer_id'                 => $order->getData('customer_id'), 
                    'number_of_orders'            => $order->getData('total_order'), 
                    'total_value_spent'           => $order->getData('grand_total'),
                    'last_promotional_email_date' => $date,
                    'customer_name'               => $order->getData('customer_firstname').' '.$order->getData('customer_lastname'),
                    'last_order_date'             => $order->getData('created_at')
                ); 
            }
          
            $salesRecovery->setData($data);
            $salesRecovery->save();
        }
  
        $number = Mage::helper('Query_SalesRecovery')->getConfig('purchase_quantity');
        $collection = Mage::getModel('Query_SalesRecovery/SalesRecovery')->getCollection();
        $collection->addFieldToFilter('number_of_orders', array('gt' =>$number));
        
        $active = Mage::helper('Query_SalesRecovery')->getConfig('active');

        $sentEmail = false;
        foreach ($collection as $value) 
        {
            $this->sendEmail($value->getData('customer_id'));
            $sentEmail = true;
        }
        return $sentEmail;

    }

    public function sendEmail($customerId)
    {      
        $templateId = Mage::helper('Query_SalesRecovery')->getConfig('email_template');
        $minimumDays = Mage::helper('Query_SalesRecovery')->getConfig('minimum_time_between_email');
        $model = Mage::getModel('Query_SalesRecovery/SalesRecovery')->load($customerId,'customer_id');

        $dates = new Zend_Date($model->getData('last_promotional_email_date'));      
        $today = new Zend_Date();     
        $maxDate = new Zend_Date($dates->addDay($minimumDays));    
        
        if($today->compare($maxDate) == 1)
        {
              $sender = array
              (
                  'name'  => Mage::helper('Query_SalesRecovery')->getConfig('email_name'),
                  'email' => Mage::helper('Query_SalesRecovery')->getConfig('email_adress')
              );

              $customer = Mage::getModel('customer/customer')->load($customerId);
              $customerName = $customer->getName();
              $customerEmail = $customer->getData('email');
              $storeId = Mage::app()->getStore()->getId();
              $mail = Mage::getModel('core/email_template');
              $couponCode = $this->_generateCouponCode(Mage::helper('Query_SalesRecovery'));
              
              $vars = array
              (
                    'customer_name'   => $customerName,
                    'discount_coupon' => $this->_generateCouponCode(Mage::helper('Query_SalesRecovery')->getConfig('discount_percentage')),
                    'coupon_code'     => $couponCode,
                    'customer_email'  => $customerEmail
              );
              
              $mail->sendTransactional($templateId, $sender, $customerEmail, $customerName, $vars, $storeId);
                                        
              $model->setData('last_promotional_email_date',$today);
              $model->save();
        }
    }  

    public function _generateCouponCode($value=0)
    {
        if(!$value)
        {
          return;
        }
        $groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
        $websiteIds = Mage::getModel('core/website')->getCollection()->getAllIds();
        $data = array(
            'product_ids' => null,
            'name' => 'Query Sales Recovery Generated Coupon - ' . date('YmdHis'),
            'description' => 'Generated by Query Sales Recovery',
            'is_active' => 1,
            'website_ids' => $websiteIds,
            'customer_group_ids' => $groupIds,
            'coupon_type' => Mage_SalesRule_Model_Rule::COUPON_TYPE_SPECIFIC,
            'coupon_code' => Mage::helper('core')->getRandomString(10),
            'uses_per_coupon' => 1,
            'uses_per_customer' => 1,
             'sort_order' => null,
            'is_rss' => 1,
            'rule' => array(
                'conditions' => array(
                    array(
                        'type' => 'salesrule/rule_condition_combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => null
                    )
                )
            ),
            'simple_action' => Mage_SalesRule_Model_Rule::BY_PERCENT_ACTION ,
            'discount_amount' => $value,
            'discount_qty' => 0,
            'discount_step' => null,
            'apply_to_shipping' => 0,
            'simple_free_shipping' => 0,
            'stop_rules_processing' => 0,
            'rule' => array(
                'actions' => array(
                    array(
                        'type' => 'salesrule/rule_condition_product_combine',
                        'aggregator' => 'all',
                        'value' => 1,
                        'new_child' => null
                    )
                )
            ),
            'store_labels' => array(Mage::helper('Query_SalesRecovery')->getConfig('discount_percentage')),
        );
        //igual o discount name o discount_percentage 
        $rule = Mage::getModel('salesrule/rule');
        $validateResult = $rule->validateData(new Varien_Object($data));
        if ($validateResult == true)
        {
            if (isset($data['simple_action']) && $data['simple_action'] == 'by_percent'
                && isset($data['discount_amount'])) {
                $data['discount_amount'] = min(100, $data['discount_amount']);
            }
            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
            }
            if (isset($data['rule']['actions'])) {
                $data['actions'] = $data['rule']['actions'];
            }   
            unset($data['rule']);
            $rule->loadPost($data);
            $rule->save();
            return $data['coupon_code'];
        }
    }
}