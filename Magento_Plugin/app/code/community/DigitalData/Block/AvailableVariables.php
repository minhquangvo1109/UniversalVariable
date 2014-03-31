<?php
/**
 * Block to handle all available variables
 *
 * @package     DigitalData
 * @author      minhquang.vo1109@gmail.com
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DigitalData_Block_AvailableVariables extends Mage_Core_Block_Template
{
    /**
     * Content origin
     */
    const CONTENT_ORIGIN = 'magento-store';

    /**
     * Page information array
     * @var array
     */
    private $_page     = array();

    /**
     * User information array
     *
     * @var array
     */
    private $_user     = array();

    /**
     * Product information array
     * @var array
     */
    private $_product  = array();

    /**
     * Category information array
     * @var array
     */
    private $_category;

    /**
     * Cart information array
     *
     * @var array
     */
    private $_cart         = array();

    /**
     * Order/purchase information array
     *
     * @var array
     */
    private $_transaction  = array();

    /**
     * Trapped event information array
     * @var array
     */
    private $_events       = array();

    /**
     * @array
     */
    public function getEvents()
    {
        return $this->_events;
    }

    /**
     * @return string
     */
    public function getMageVersion() {
        return Mage::getVersion();
    }

    /**
     * @return string
     */
    public function getPageInstanceId()
    {
        return Mage::helper('core/url')->getCurrentUrl();
    }

    /**
     * @return array
     */
    public function getPageInformation()
    {
        return $this->_page;
    }

    /**
     * @return array
     */
    public function getUserInformation()
    {
        return $this->_user;
    }

    /**
     * @return array
     */
    public function getProductInformation()
    {
        return $this->_product;
    }

    /**
     * @return array
     */
    public function getCategoryInformation()
    {
        return $this->_category;
    }

    /**
     * @return array
     */
    public function getTransactionInformation()
    {
        return $this->_transaction;
    }

    /**
     * @return array
     */
    public function getCartInformation()
    {
        return $this->_cart;
    }

    /**
     * Prepare all useful information relative to page
     */
    public function preparePageInformation()
    {
        $helper = Mage::helper('digitaldata');

        // Get head block in order to get page title
        $name = false;
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            if ($headBlock->getTitle()) {
                $name = $headBlock->getTitle();
            }
        }

        // if page is a cms one, retrieve page identifier
        $pageId = false;
        $currentCmsPage = Mage::getSingleton('cms/page');
        if ($currentCmsPage) {
            $pageId = $currentCmsPage->getIdentifier();
        }

        // prepare useful page information
        $this->_page = array();
        $this->_page['pageInfo'] = array(
            'pageID'          => $pageId ? $pageId : null,
            'pageName'        => $name ? $name : null,
            'destinationURL'  => $this->getPageInstanceId(),
            'referringURL'    => Mage::helper('core/http')->getHttpReferer(),
            'breadCrumbs'     => $helper->getPageBreadcrumb(),
            'language'        => Mage::app()->getLocale()->getLocaleCode(),
            'geoRegion'       => Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_COUNTRY),

            // this part is specific to SmartFocus Advisor product
            'contentOrigin' => self::CONTENT_ORIGIN, // Magento store
            'stores'        => Mage::app()->getStore()->getId(), // store where user is now
            'version'       => $this->getMageVersion(), // Magento version
        );

        // retrieve all category information
        $this->_page['category'] = $this->getProductCategories();
        // page type
        $this->_page['category']['pageType'] = $helper->getPageType();
    }

    /**
     * Prepare all useful information relative to current user (registered or only guest
     */
    public function prepareUserInformation()
    {
        $helper = Mage::helper('digitaldata');
        // segment information array
        $segment = array(
            'newsletterStatus' => false, // indicate whether the user is subsribed to newsletter list
            'customerGroup'    => '' // indicate which Magento customer group user belongs to
        );

        // get customer from session
        $customer = Mage::helper('customer')->getCustomer();

        $profileInfo = array();
        $profileInfo['returningStatus'] = false;
        // locale code (en_US, fr_FR) is retrieved from current store
        $profileInfo['language'] = Mage::app()->getLocale()->getLocaleCode();

        // prepare customer email
        $email = $customer->getEmail();
        if (!$email) {
            if ($helper->isConfirmation()) {
                // get email saved from order in session
                $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
                if ($orderId) {
                    $order = Mage::getModel('sales/order')->load($orderId);
                    $email = $order->getCustomerEmail();
                }
            }
        }
        if ($email) {
            $profileInfo['email'] = $email;
            $subscriber = Mage::getModel('newsletter/subscriber')->loadByEmail($email);
            if (
                $subscriber->getEntityId()
                && $subscriber->getSubscriberStatus() == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED
            ) {
                // if a subscriber with the same email is found, it means user has subscribed to the newsletter list
                $segment['newsletterStatus'] = true;
            }
        }

        if ($customer->getEntityId()) {
            $profileInfo['profileID']       = $customer->getEntityId();
            // if user has a registered account, it means he is returning to our store
            $profileInfo['returningStatus'] = true;

            $segment['customerGroup'] = Mage::getModel('customer/group')
                ->load($customer->getGroupId())->getCustomerGroupCode();
        }

        $this->_user = array(
            array(
                'segment' => $segment,
                'profile' => array(
                     array('profileInfo' => $profileInfo)
                )
            )
        );
    }

    /**
     * Get product categories
     *
     * @return array
     */
    public function getProductCategories() {
        if (!$this->_category) {
            $this->_category = array();

            // prepare category information from breadcrumb path
            $path = Mage::helper('catalog')->getBreadcrumbPath();

            /**
             * Here in our case, we are only interested in category id
             * You can adapt the code to your usage
             */
            $i = 1;
            foreach ($path as $key => $data) {
                $pos = strpos($key, "category");
                // only get category id
                if ($pos !== false) {
                    if (!isset($this->_category['primaryCategory'])) {
                        $this->_category['primaryCategory'] = substr($key, $pos+8);
                    } else {
                        $this->_category['subCategory' . $i] = substr($key, $pos+8);
                        $i++;
                    }
                }
            }

            // in case of advanced filter (product search etc ...)
            $categoryFilter = Mage::registry('current_category_filter');
            if ($categoryFilter) {
                $key = 'subCategory' . ($i + 1);
                if ($i == 1) {
                    $key = 'primaryCategory';
                }
                $this->_category[$key] = $categoryFilter->getId();
            }
        }

        return $this->_category;
    }

    /**
     * Get useful information relative to products that are linked to a current product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getLinkedProductInformation(Mage_Catalog_Model_Product $product)
    {
        /* Linked products Handling */
        $linked = array();
        // get all related products for grouped product
        if ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_GROUPED) {
            foreach ($product->getTypeInstance()->getAssociatedProducts($product) as $linkedProd) {
                $productInfo = array(
                    'productID'         => $linkedProd->getId(),
                    'productName'       => $linkedProd->getName(),
                    'sku'               => $linkedProd->getSku(),
                );
                $linked[] = array('productInfo' => $productInfo);
            }
        } elseif ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            // get all related products for configurable product
            foreach ($product->getTypeInstance()->getUsedProductCollection($product) as $linkedProd) {
                $productInfo = array(
                    'productID'         => $linkedProd->getId(),
                    'productName'       => $linkedProd->getName(),
                    'sku'               => $linkedProd->getSku(),
                );
                $linked[] = array('productInfo' => $productInfo);
            }
        } elseif ($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            // get all related products for bundle product
            $optIDs = $product->getTypeInstance()->getOptionsIds($product);
            foreach ($product->getTypeInstance()->getSelectionsCollection($optIDs, $product) as $linkedProd) {
                $productInfo = array(
                    'productID'         => $linkedProd->getId(),
                    'productName'       => $linkedProd->getName(),
                    'sku'               => $linkedProd->getSku(),
                );
                $linked[] = array('productInfo' => $productInfo);
            }
        }
        return $linked;
    }

    /**
     * Prepare useful information relative to current product
     */
    public function prepareProductInformation()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::helper('digitaldata')->getProduct();

        if ($product->getId()) {
            $searchable = false;
            // check if product visibility is product search
            if (in_array(
                $product->getVisibility(),
                Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds()
            )) {
                $searchable = true;
            }

            $productInfo = array(
                'productID'         => $product->getId(),
                'productName'       => $product->getName(),
                'sku'               => $product->getSku(),
                'description'       => $product->getShortDescription(),

                /**
                    Activate brand data if you have brand attributs
                    'brand'             => $product->getAttributeText('brand'),
                */

                'productType'       => $product->getTypeId(),
                'currency'          => Mage::app()->getStore()->getCurrentCurrencyCode(),
                'unitPrice'         => (float) $product->getPrice(),
                'salePrice'         => (float) $product->getFinalPrice(),
                'isInStock'         => $product->getIsSalable(),
                'visibility'        => Mage_Catalog_Model_Product_Visibility::getOptionText( $product->getVisibility()),
                'searchable'        => $searchable,

                // category information
                'category'          => $this->getProductCategories(),

                // linked products information
                'linkedProduct'     => $this->getLinkedProductInformation($product),
            );

            $this->_product = array(array('productInfo' => $productInfo));
        }
    }

    /**
     * Prepare useful information relative to products that are present on the page (category page, or catalog search)
     */
    public function prepareProductListing()
    {
        $layer = $this->getCatalogLayer();
        if ($layer) {
            $productCollection = $layer->getProductCollection();
            $presentProducts = array();

            if ($productCollection) {
                foreach($productCollection as $product) {
                    $presentProducts[] = array(
                        'productInfo' => array(
                            'productID'         => $product->getId(),
                            'productName'       => $product->getName(),
                            'sku'               => $product->getSku(),
                        )
                    );
                }
                if (count($presentProducts)) {
                    if (is_array($this->_page) && isset($this->_page['pageInfo'])) {
                        $this->_page['pageInfo']['presentProducts'] = $presentProducts;
                    }
                }
            }
        }
    }

    /**
     * Get address information
     *
     * @param Mage_Sales_Model_Order_Address $address
     * @return array
     */
    public function getAddressInfo(Mage_Sales_Model_Order_Address $address)
    {
        $info = array();
        if ($address) {
          $info['name']             = $address->getName();
          $info['address']          = $address->getStreetFull();
          $info['city']             = $address->getCity();
          $info['postalCode']       = $address->getPostcode();
          $info['country']          = $address->getCountry();
          $state                    = $address->getRegion();
          $info['stateProvince']    = $state ? $state : '';
        }
        return $info;
    }

    /**
     * Get catalog layer, from product page, product listing or product search page
     * @return Mage_Catalog_Model_Layer | null
     */
    public function getCatalogLayer()
    {
        $layer = Mage::registry('current_layer');
        $helper = Mage::helper('digitaldata');

        if (!$layer) {
            // only prepare product listing for category if we don't have any predefined layer
            if ($helper->isCategory()) {
                $layer = Mage::getSingleton('catalog/layer');
            } else if ($helper->isAdvancedSearch()) {
                $layer = Mage::getSingleton('catalogsearch/advanced');
            }
        }

        return $layer;
    }

    /**
     * Get useful information relative to line item (quote item or order item)
     *
     * @param Varien_Object $item
     * @return array
     */
    public function getLineItemInformation(Varien_Object $item)
    {
        $info = array();

        $info['quantity'] = $item->getQtyOrdered() ? (float) $item->getQtyOrdered() : (float) $item->getQty();
        $info['price']    = array(
            'basePrice'                => (float) $item->getPrice(),
            'voucherDiscount'          => (float) $item->getDiscountPercent(),
            'currency'                 => Mage::app()->getStore()->getCurrentCurrencyCode(),
            'taxRate'                  => (float) $item->getTaxPercent(),
            'priceWithTax'             => (float) $item->getPriceInclTax(),
            'rowTotal'                 => (float) $item->getRowTotal(),
            'rowTotalInclTax'          => (float) $item->getRowTotalInclTax()
        );
        $info['productInfo'] = array(
            'productID'         => $item->getProductId(),
            'productName'       => $item->getName(),
            'sku'               => $item->getSku(),
        );

        return $info;
    }

    /**
     * Determine whether the subtotal contains the tax
     * @param unknown $order
     * @param unknown $tax
     * @return boolean
     */
    private function _doesSubtotalIncludeTax($order, $tax) {
        /* Conditions:
            - if tax is zero, then set to false
            - Assume that if grand total is bigger than total after subtracting shipping, then subtotal does NOT include tax
        */
        $grandTotalWithoutShipping = $order->getGrandTotal() - $order->getShippingAmount();
        if ($tax == 0 || $grandTotalWithoutShipping > $order->getSubtotal()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Prepare cart information, the information will be available in _cart variable
     */
    public function prepareCart()
    {
        $this->_cart = array();
        $cart = Mage::getSingleton('checkout/session');
        if ($cart) {
            $quote = $cart->getQuote();
            if ($quote && $quote->getId()) {
                $cartItems = array();
                foreach($quote->getAllVisibleItems() as $item) {
                    $cartItems[] = $this->getLineItemInformation($item);
                }

                $tax = (float) $quote->getShippingAddress()->getTaxAmount();
                $this->_cart = array(
                    'cartID' => $cart->getQuoteId(),
                    'price'  => array(
                        'basePrice'             => (float) $quote->getSubtotal(),
                        'voucherCode'           => $quote->getCouponCode(),
                        'voucherDescription'    => $quote->getShippingAddress()->getDiscountDescription(),
                        'voucherDiscountAmount' => (float)$quote->getShippingAddress()->getDiscountAmount(),
                        'subtotalWithDiscount'  => (float) $quote->getSubtotalWithDiscount(),
                        'currency'              => Mage::app()->getStore()->getCurrentCurrencyCode(),
                        'tax'                   => $tax,
                        'subtotalIncludeTax'    => (boolean) $this->_doesSubtotalIncludeTax($quote, $tax),
                        'shipping'              => (float) $quote->getShippingAddress()->getShippingAmount(),
                        'shippingMethod'        => $quote->getShippingAddress()->getShippingDescription(),
                        'cartTotal'             => (float) $quote->getGrandTotal()
                    ),
                    'item' => $cartItems
                );
            }
        }
    }

    /**
     * Prepare useful information relative to the last purchased order
     */
    public function prepareTransaction()
    {
        $session = Mage::getSingleton('checkout/session');
        if ($session && $orderId = $session->getLastOrderId()) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $transactionItems = array();
            foreach($order->getAllVisibleItems() as $item) {
                $transactionItems[] = $this->getLineItemInformation($item);
            }

            $this->_transaction = array(
              'transactionID'         => $order->getIncrementId(),
              'profile'               => array(
                  'profileInfo'   => array(
                      'profileID' => $order->getCustomerId(),
                      'email'     => $order->getCustomerEmail(),
                  ),
                  'address'           => $this->getAddressInfo($order->getBillingAddress()),
                  'shippingAddress'   => $this->getAddressInfo($order->getShippingAddress()),
              ),
              'total'               => array(
                  'basePrice'                => (float) $order->getSubtotal(),
                  'voucherCode'              => $order->getCouponCode(),
                  'voucherDiscountAmmount'   => (float) $order->getDiscountAmount(),
                  'currency'                 => Mage::app()->getStore()->getCurrentCurrencyCode(),
                  'tax'                      => (float) $order->getTaxAmount(),
                  'shipping'                 => (float) $order->getShippingAmount(),
                  'shippingMethod'           => $order->getShippingDescription(),
                  'priceWithTax'             => (float) $order->getSubtotalInclTax(),
                  'transactionTotal'         => (float) $order->getGrandTotal(),
                  'paymentType'              => $order->getPayment()->getMethodInstance()->getTitle()
              ),
              'item' => $transactionItems
           );
        }
    }

    /**
     * Prepare search information
     */
    public function prepareSearchInformation()
    {
        if (isset($this->_page['pageInfo'])) {
            if (!Mage::helper('digitaldata')->isAdvancedSearch()) {
                // in normal search

                $query = Mage::helper('catalogsearch')->getQuery();
                $searchText = array($query->getQueryText());

                if ($this->getCatalogLayer()) {
                    $state = $this->getCatalogLayer()->getState();
                    foreach ($state->getFilters() as $filter) {
                        $searchText[] = strip_tags($filter->getName()). ':' . strip_tags($filter->getLabel());
                    }
                }
                // prepare search text, and the number of results
                $this->_page['pageInfo']['onsiteSearchTerm'] = implode(';', $searchText);
                $this->_page['pageInfo']['onsiteSearchResults'] = $query->getNumResults();

            } else {
                // in advanced search
                $searchCriterias = Mage::getSingleton('catalogsearch/advanced')->getSearchCriterias();
                if ($searchCriterias) {
                    $values = array();
                    foreach ($searchCriterias as $criteria) {
                         $values[] = strip_tags($criteria['name']) . ':'. strip_tags($criteria['value']);
                    }
                    $this->_page['pageInfo']['onsiteSearchTerm'] = implode(';', $values);

                    // if we can retrieve catalog layer
                    if ($this->getCatalogLayer()) {
                        $collection = $this->getCatalogLayer()->getProductCollection();
                        if ($collection) {
                            // the number of results is the size of returned product collection
                            $this->_page['pageInfo']['onsiteSearchResults'] = $collection->getSize();
                        }
                    }
                }
            }
        }
    }

    /**
     * Prepare trapped events
     */
    public function prepareEvents()
    {
        $this->_events = Mage::helper('digitaldata')->getEvents();
        Mage::helper('digitaldata')->clearEvents();
    }

    /**
     * Prepare all variables useful for the current page
     */
    public function prepareVariables()
    {
        /* @var $helper DigitalData_Helper_Data*/
        $helper = Mage::helper('digitaldata');

        $this->preparePageInformation();
        $this->prepareEvents();
        $this->prepareUserInformation();
        $this->prepareProductListing();

        if ($helper->isSearch()) {
            $this->prepareSearchInformation();
        }
        if ($helper->isProduct()) {
            $this->prepareProductInformation();
        }

        if (!$helper->isConfirmation()) {
            $this->prepareCart();
        } else {
            $this->prepareTransaction();
        }
    }

    /**
     * Render our block
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->prepareVariables();
        return parent::_toHtml();
    }
}
