<?php

namespace Space48\SubTech\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Json\Helper\Data as jsonHelper;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Space48\SubTech\Helper\Address;
use Space48\SubTech\Helper\Data;

class CustomerAddressSaveAfterObserver implements ObserverInterface
{

    const CUSTOMER_DATA_COOKIE_NAME = "customerData";
    private $cookieManager;
    private $cookieMetadataFactory;
    private $addressHelper;
    private $jsonHelper;
    private $sub2Helper;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Address $addressHelper,
        Data $sub2Helper,
        jsonHelper $jsonHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->addressHelper = $addressHelper;
        $this->jsonHelper = $jsonHelper;
        $this->sub2Helper = $sub2Helper;
    }

    public function execute(Observer $observer)
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

    private function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }
}
