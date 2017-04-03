<?php

class Loewenstark_RemoveUnconfirmedCustomers_Model_Observer
extends Mage_Core_Model_Abstract
{
    public function run()
    {
        $date = date('Y-m-d H:i:s', strtotime('-1 week'));

        $customers = Mage::getModel('customer/customer')->getCollection()
            ->addAttributeToSelect(array('email', 'confirmation'))
            ->addAttributeToFilter('confirmation', array('notnull' => false))
            ->addAttributeToFilter('updated_at', array('lteq' => $date))
            ->addOrder('updated_at', 'ASC')
        ;

        foreach ($customers->getAllIds() as $_id)
        {
            $customer = Mage::getModel('customer/customer')->load((int) $_id);
            /* @var $customer Mage_Customer_Model_Customer */
            if ($customer->getId() && strlen((string) $customer->getData('confirmation')) > 3)
            {
                try {
                    $customer->delete();
                    Mage::log('Customer: "'. $customer->getEmail(). '" has been deleted.', null, 'customer_removed.log');
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }
}