<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Space48\SubTech\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLoginSuccessObserver implements ObserverInterface
{
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

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getData('model');
        $mergedData = array_merge($customer->getData(), $this->addressHelper->getCustomerAddressData($customer));
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
