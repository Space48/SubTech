<?php

namespace Space48\SubTech\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerAddressSaveAfterObserver implements ObserverInterface {

    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $addressHelper;
    const CUSTOMER_DATA_COOKIE_NAME = "customerData";

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Space48\SubTech\Helper\Address $addressHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->addressHelper = $addressHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {

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
            json_encode($mappedData),
            $publicCookieMetadata
        );

        return $this;
    }

}
