<?php

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template;
use Space48\GtmDataLayer\Helper\Data as GtmHelper;

class OrderSuccess extends Template {

    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    protected $cookieHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $salesOrderCollection;

    /**
     * Google Tag Manager Helper
     *
     * @var \Space48\GtmDataLayer\Helper\Data
     */
    protected $gtmHelper = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param GtmHelper $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        GtmHelper $gtmHelper,
        array $data = []
    ) {
        $this->cookieHelper = $cookieHelper;
        $this->jsonHelper = $jsonHelper;
        $this->gtmHelper = $gtmHelper;
        $this->registry = $registry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->salesOrderCollection = $salesOrderCollection;
        $this->defaultCategoryName = $this->gtmHelper->getConfig("item_category");

        parent::__construct(
            $context,
            $data
        );
    }

    protected function _toHtml()
    {
        if (!$this->gtmHelper->isTypeEnabled(array('order_success'))) {
            return '';
        }

        return $this->getOutput();
    }

    public function getOutput()
    {
        $result = [];
        $orderIds = $this->registry->registry('orderIds');

        if (empty($orderIds) || !is_array($orderIds)) {
            return "";
        }

        $orderCollection = $this->salesOrderCollection->create();
        $orderCollection->addFieldToFilter('entity_id', ['in' => $orderIds]);

        foreach ($orderCollection as $order) {

            // Assign order data to array

            $shippingAddress = $order->getShippingAddress();

            $orderData = [];
            $orderData['OrderID'] = $order['increment_id'];
            $orderData['Affiliation'] = "";
            $orderData['Total'] = $order['grand_total'];
            $orderData['Tax'] = $order['tax_amount'];
            $orderData['Shipping'] = $order['base_shipping_amount'];
            $orderData['Currency'] = $order['base_currency_code'];
            $orderData['City'] = $shippingAddress['city'];
            $orderData['County'] = $shippingAddress['region'];
            $orderData['Country'] = $shippingAddress['country_id'];

            $result[] = "__s2tQ.push(['addOrder' ," .
                $this->jsonHelper->jsonEncode(array_filter($orderData)) . "]);\n";

            // Assign store (customer) data to array

            $storeData = [];
            $storeData['Title'] = $shippingAddress['prefix'];
            $storeData['Forename'] = $order['customer_firstname'];
            $storeData['Surname'] = $order['customer_lastname'];
            $storeData['Address1'] = $shippingAddress['street'];
            $storeData['Address2'] = $shippingAddress['city'];
            $storeData['Address3'] = $shippingAddress['region'];
            $storeData['Address4'] = $shippingAddress['country_id'];
            $storeData['Postcode'] = $shippingAddress['postcode'];
            $storeData['Landline'] = $shippingAddress['telephone'];
            $storeData['Mobile'] = "";
            $storeData['Email'] = $order['customer_email'];
            $storeData['Optout1P'] = "";
            $storeData['Optout3P'] = "";

            $result[] = "__s2tQ.push(['storeData' ," .
                $this->jsonHelper->jsonEncode(array_filter($storeData)) . "]);\n";

            // Assign order items data to array

            /** @var \Magento\Sales\Model\Order\Item $item*/
            foreach ($order->getAllVisibleItems() as $item) {

                $product = [];
                $productEntity = $this->getProductById($item->getProductId());

                $product['OrderID'] = $order['increment_id'];
                $product['SKU'] = $item->getSku();
                $product['Product_ID'] = $item->getProductId();
                $product['Product_Name'] = $item->getName();
                $product['Category'] = $this->getCategoryName($productEntity);
                $product['Unit_Price'] = $item->getBasePrice();
                $product['Quantity'] = $item->getQtyOrdered();
                $product['Size'] = '';
                $product['Color'] = '';

                $result[] = "__s2tQ.push(['addItem' ," .
                    $this->jsonHelper->jsonEncode(array_filter($product)) . "]);\n";

            }
        }

        return implode("\n", $result);
    }

    public function getProductById($productId)
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToFilter('entity_id', $productId)
            ->addAttributeToSelect('name')
            ->setPageSize(1)
            ->getFirstItem();
    }

    public function getCategoryName($product)
    {
        $categories = $product->getCategoryIds();
        $categoryName = null;

        if(!empty($categories)){
            $category = $this->getFirstCategory($categories);
            $categoryName = $category->getName();
        }

        return is_null($categoryName) ? $this->defaultCategoryName : $categoryName;
    }

    public function getFirstCategory($categoryIds)
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('entity_id', array("in" => $categoryIds))
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('name')
            ->setPageSize(1)
            ->getFirstItem();
    }
}