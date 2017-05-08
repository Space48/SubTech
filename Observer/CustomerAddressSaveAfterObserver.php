<?php

namespace Space48\SubTech\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerAddressSaveAfterObserver implements ObserverInterface {

    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $addressHelper;
    protected $jsonHelper;
    protected $sub2Helper;
    const CUSTOMER_DATA_COOKIE_NAME = "customerData";

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Space48\SubTech\Helper\Address $addressHelper,
        \Space48\SubTech\Helper\Data $sub2Helper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->addressHelper = $addressHelper;
        $this->jsonHelper = $jsonHelper;
        $this->sub2Helper = $sub2Helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $customerAddress = $observer->getCustomerAddress();
        $customer = $customerAddress->getCustomer();
        $mergedData = array_merge($customer->getData(), $observer->getCustomerAddress()->getData());
        $mappedData = $this->addressHelper->mapCustomerData($mergedData);

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration(3600)
            ->setPath('/')
            ->setHttpOnly(false);

        $this->cookieManager->setPublicCookie(
            self::CUSTOMER_DATA_COOKIE_NAME,
            $this->jsonHelper->jsonEncode($mappedData),
            $publicCookieMetadata
        );

        return $this;
    }

    public function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }

}
