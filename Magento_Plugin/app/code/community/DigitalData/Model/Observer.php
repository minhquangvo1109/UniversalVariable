<?php
/**
 * Observer to handle User Action, then create appropriate events
 *
 * @package     DigitalData
 * @author      minhquang.vo1109@gmail.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DigitalData_Model_Observer
{
    /**
     * Constants for different event types
     */
    const CUSTOMER_EVENT_TYPE     = 'customer';
    const CART_EVENT_TYPE         = 'cart';
    const NEWSLETTER_EVENT_TYPE   = 'newsletter';

    /**
     * Handle customer login action, event information will be saved into current session
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        /* User */
        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn()) {
            $event = array();
            $event['eventInfo'] = array(
                'eventName'     => 'Customer Login',
                'eventAction'   => 'login',
                'type'          => self::CUSTOMER_EVENT_TYPE,
                'timeStamp'     => Mage::getModel('core/date')->timestamp(),
            );
            // save event information into session variable
            Mage::helper('digitaldata')->saveEventInformation($event);
        }
    }

    /**
     * Handle the cart modification action (add product, modify product quantity)
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteItemSaveAfter(Varien_Event_Observer $observer)
    {
        /*@var $item Mage_Sales_Model_Quote */
        $item = $observer->getItem();
        if (!$item->getSku()) {
            return;
        }

        /* Compare Quantities (before and after modification) */
        $startQty = (int) $item->getOrigData('qty');
        $endQty   = (int) $item->getData('qty');

        /* Check if the quantity has changed */
        if ($startQty == $endQty) {
            return;
        }

        $event = array('eventInfo' => array());
        $event['eventInfo']['type']      = self::CART_EVENT_TYPE;
        $event['eventInfo']['timeStamp'] = Mage::getModel('core/date')->timestamp();
        $event['attribute'] = array(
            'sku'         => $item->getSku(),
            'productName' => $item->getName(),
            'productID'   => $item->getId(),
            'orginalQty'  => $startQty,
            'actualQty'   => $endQty
        );

        if ($startQty == 0) {
            // Added to cart
            $event['eventInfo']['eventName']   = 'Cart Add';
            $event['eventInfo']['eventAction'] = 'add';
        } else if ($endQty == 0) {
            // Removed from cart
            $event['eventInfo']['eventName']   = 'Cart Remove';
            $event['eventInfo']['eventAction'] = 'remove';
        } else {
            // Changed in quantities
            $event['eventInfo']['eventName']   = 'Cart Change Quantities';
            $event['eventInfo']['eventAction'] = 'modify_quantity';
        }

        // save event information into session variable
        Mage::helper('digitaldata')->saveEventInformation($event);
    }

    /**
     * Handle remove product from cart action
     *
     * @param Varien_Event_Observer $observer
     */
    public function salesQuoteItemDeleteAfter(Varien_Event_Observer $observer)
    {
            /*@var $item Mage_Sales_Model_Quote */
        $item = $observer->getItem();
        if (!$item->getSku()) {
            return;
        }

        $event = array('eventInfo' => array());
        $event['eventInfo']['type']      = self::CART_EVENT_TYPE;
        $event['eventInfo']['timeStamp'] = Mage::getModel('core/date')->timestamp();
        $event['attribute'] = array(
            'sku'         => $item->getSku(),
            'productName' => $item->getName(),
            'productID'   => $item->getId(),
            'orginalQty'  => $startQty,
            'actualQty'   => 0
        );
        $event['eventInfo']['eventName']   = 'Cart Remove';
        $event['eventInfo']['eventAction'] = 'remove';

        // save event information into session variable
        Mage::helper('digitaldata')->saveEventInformation($event);
    }

    /**
     * Handle the subscription action
     *
     * @param Varien_Event_Observer $observer
     */
    public function subscribeCustomer(Varien_Event_Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        if (!$subscriber) {
            return;
        }

        // detect whether there is a change
        if ($subscriber->getIsStatusChanged()) {
            $event = array();
            $event['eventInfo']['eventType'] = self::NEWSLETTER_EVENT_TYPE;
            $event['eventInfo']['timeStamp'] = Mage::getModel('core/date')->timestamp();
            $event['attribute']['email']     = $subscriber->getSubscriberEmail();

            switch ($subscriber->getSubscriberStatus()) {
                case Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED:
                     $event['eventInfo']['eventName']   = 'Newsletter Unsubscription';
                     $event['eventInfo']['eventAction'] = 'unsubscribe';
                break;
                case Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED:
                     $event['eventInfo']['eventName']   = 'Newsletter Subscription';
                     $event['eventInfo']['eventAction'] = 'subscribe';
                break;
            }
            // save event information into session variable
            Mage::helper('digitaldata')->saveEventInformation($event);
        }
    }

    /**
     * Handle the customer unsubscription action
     *
     * @param Varien_Event_Observer $observer
     */
    public function unsubscribeCustomer(Varien_Event_Observer $observer)
    {
        $subscriber = $observer->getEvent()->getSubscriber();
        if (!$subscriber) {
            return;
        }

        $event = array();
        $event['eventInfo']['eventType']   = self::NEWSLETTER_EVENT_TYPE;
        $event['eventInfo']['timeStamp']   = Mage::getModel('core/date')->timestamp();
        $event['attribute']['email']       = $subscriber->getSubscriberEmail();
        $event['eventInfo']['eventName']   = 'Newsletter Unsubscription';
        $event['eventInfo']['eventAction'] = 'unsubscribe';

        // save event information into session variable
        Mage::helper('digitaldata')->saveEventInformation($event);
    }

    /**
     * Handle the customer creation action
     *
     * @param Varien_Event_Observer $observer
     */
    public function createCustomer(Varien_Event_Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        if (!$customer) {
            return;
        }

        if ($customer->isObjectNew()) {
            $event = array();
            $event['eventInfo']['eventType']       = self::CUSTOMER_EVENT_TYPE;
            $event['eventInfo']['timeStamp']       = Mage::getModel('core/date')->timestamp();
            $event['attribute']['email']           = $customer->getEmail();
            $event['attribute']['profileID']       = $customer->getEntityId();
            $event['eventInfo']['eventName']       = 'Account Creation';
            $event['eventInfo']['eventAction']     = 'create';
            // save event information into session variable
            Mage::helper('digitaldata')->saveEventInformation($event);
        }
    }
}