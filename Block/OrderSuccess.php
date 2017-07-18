<?php

namespace Space48\SubTech\Block;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Space48\GtmDataLayer\Helper\Data as GtmHelper;

class OrderSuccess extends Template
{

    /**
     * @var Data
     */
    private $jsonHelper;

    /**
     * @var OrderCollectionFactory
     */
    private $salesOrderCollection;

    /**
     * Google Tag Manager Helper
     *
     * @var \Space48\GtmDataLayer\Helper\Data
     */
    private $gtmHelper = null;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var OrderCollectionFactory
     */
    private $categoryCollectionFactory;

    private $defaultCategoryName;

    /**
     * @param Context                   $context
     * @param OrderCollectionFactory    $salesOrderCollection
     * @param Data                      $jsonHelper
     * @param Registry                  $registry
     * @param ProductCollectionFactory  $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param GtmHelper                 $gtmHelper
     * @param array                     $data
     *
     */
    public function __construct(
        Context $context,
        OrderCollectionFactory $salesOrderCollection,
        Data $jsonHelper,
        Registry $registry,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        GtmHelper $gtmHelper,
        array $data = []
    ) {
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

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->gtmHelper->isTypeEnabled(['order_success'])) {
            return '';
        }

        return $this->getOutput();
    }

    /**
     * @return string
     */
    private function getOutput()
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
            $orderData['Affiliation'] = $order['coupon_rule_name'];
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

            /** @var \Magento\Sales\Model\Order\Item $item */
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

    /**
     * @param $productId
     *
     * @return \Magento\Framework\DataObject | \Magento\Catalog\Model\Product
     */
    private function getProductById($productId)
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToFilter('entity_id', $productId)
            ->addAttributeToSelect('name')
            ->setPageSize(1)
            ->getFirstItem();
    }

    /**
     * @param $product \Magento\Catalog\Model\Product
     *
     * @return mixed|null
     */
    private function getCategoryName($product)
    {
        $categories = $product->getCategoryIds();
        $categoryName = null;

        if (!empty($categories)) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->getFirstCategory($categories);
            $categoryName = $category->getName();
        }

        return $categoryName == null ? $this->defaultCategoryName : $categoryName;
    }

    /**
     * @param $categoryIds
     *
     * @return \Magento\Framework\DataObject | \Magento\Catalog\Model\Category
     */
    private function getFirstCategory($categoryIds)
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToFilter('entity_id', ["in" => $categoryIds])
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('name')
            ->setPageSize(1)
            ->getFirstItem();
    }
}
