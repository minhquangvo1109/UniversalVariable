<?php
/**
 * Helper class
 *
 * @package     DigitalData
 * @author      minhquang.vo1109@gmail.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DigitalData_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Referer string
     * @var string
     */
    protected $_refererKeyword = null;

    /**
     * Is the curret page homepage ?
     *
     * @return boolean
     */
    public function isHome() {
        if ($this->_getRequest()->getRequestString() == "/") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is the curret page a cms page ?
     *
     * @return boolean
     */
    public function isContent() {
        if ($this->_getRequest()->getModuleName() == 'cms') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is the curret page a category page ?
     *
     * @return boolean
     */
    public function isCategory() {
        if ($this->_getRequest()->getControllerName() == 'category') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is the curret page search page ?
     *
     * @return boolean
     */
    public function isSearch() {
        if ($this->_getRequest()->getModuleName() == 'catalogsearch') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Is the curret page an advanced search page ?
     *
     * @return boolean
     */
    public function isAdvancedSearch()
    {
        if ($this->_getRequest()->getModuleName() == 'catalogsearch' && $this->_getRequest()->getControllerName() == 'advanced') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return current category object from the global registry
     *
     * @return Mage_Catalog_Model_Category|null
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Retrieve current Product object from the global registry
     *
     * @return Mage_Catalog_Model_Product|null
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Is the curret page a product page ?
     *
     * @return boolean
     */
    public function isProduct() {
        $onCatalog = false;
        if ($this->getProduct()) {
            $onCatalog = true;
        }
        return $onCatalog;
    }

    /**
     * Is the curret page the cart page ?
     *
     * @return boolean
     */
    public function isBasket() {
        $request = $this->_getRequest();
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if ($module == 'checkout' && $controller == 'cart' && $action == 'index'){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Are we at checkout page ?
     * @return boolean
     */
    public function isCheckout() {
        if (
            strpos($this->_getRequest()->getModuleName(), 'checkout') !== false
            && $this->_getRequest()->getActionName() != 'success'
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Are we at the confirmation order page ?
     *
     * @return boolean
     */
    public function isConfirmation() {
        // default controllerName is "onepage"
        // relax the check, only check if contains checkout
        // somecheckout systems has different prefix/postfix,
        // but all contains checkout
        if (strpos($this->_getRequest()->getModuleName(), 'checkout') !== false && $this->_getRequest()->getActionName() == "success") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Are we at the 404 page ?
     *
     * @return boolean
     */
    public function is404Page()
    {
        if ($this->_getRequest()->getActionName() == "noRoute") {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get the page type
     *
     * @return string
     */
    public function getPageType() {
        if ($this->isHome()) {
            return 'home';
        } elseif($this->is404Page()) {
            return '404';
        } elseif ($this->isContent()) {
            return 'content';
        } elseif ($this->isCategory()) {
            return 'category';
        } elseif ($this->isSearch()) {
            return 'search';
        } elseif ($this->isProduct()) {
            return 'product';
        } elseif ($this->isBasket()) {
            return 'basket';
        } elseif ($this->isCheckout()) {
            return 'checkout';
        } elseif ($this->isConfirmation()) {
            return 'confirmation';
        }

        return $this->_getRequest()->getModuleName();
    }

    /**
     * Get bread crumb path
     *
     * return array
     */
    public function getBreadcrumb() {
        return Mage::helper('catalog')->getBreadcrumbPath();
    }

    /**
     * Get page bread crumb - list of categories
     *
     * @return array
     */
    public function getPageBreadcrumb() {
        $arr = $this->getBreadcrumb();
        $breadcrumb = array();
        foreach ($arr as $category) {
            $breadcrumb[] = $category['label'];
        }
        return $breadcrumb;
    }

    /**
     * Get referer criteria
     *
     * @return string
     */
    public function getRefererCriteria() {
        $criteria = '';

        if ($this->_refererKeyword !='') {
            $request = Mage::app()->getRequest();
            $referralTerm = $request->getParam($this->_refererKeyword);
            if ($referralTerm!='') {
                $criteria = Mage::helper('core')->urlDecode($referralTerm);
            }
        } else {
            // we are still looking for referral term in both scenarios if referrerCode was left blank
            $httpReferer = Mage::helper('core/http')->getHttpReferer();
            // in the plugin config or if no referral term was passed
            if (isset($httpReferer)) {
                $ref = parse_url($httpReferer);
                if (is_array($ref) && isset($ref['host']) && isset($ref['query'])) {
                    if (preg_match('/(google|yahoo|bing)/', $ref['host'])) {
                        $res = array();
                        if (preg_match('/[pq]=([^\&]*)/', $ref['query'], $res)) {
                            $criteria = Mage::helper('core')->urlDecode($res[1]);
                        }
                    }
                }
            }
        }

        return $criteria;
    }

    /**
     * Get customer session
     *
     * @return Mage_Customer_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Get all saved events in customer session, then return the unserialized data
     *
     * @return array
     */
    public function getEvents()
    {
        $session = $this->getSession();
        return unserialize($session->getEvents());
    }

    /**
     * Save event data in serialized form then return the original event array
     *
     * @return array
     */
    public function saveEventInformation(array $event)
    {
        $events = $this->getEvents();
        $events[] = $event;
        $this->getSession()->setData('events', serialize($events));
        return $events;
    }

    /**
     * Clear event data from customer session
     *
     * @return array
     */
    public function clearEvents()
    {
        $this->getSession()->setData('events', null);
    }
}
