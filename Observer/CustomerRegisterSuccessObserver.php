<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Space48\SubTech\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Space48\SubTech\Helper\Address;
use Space48\SubTech\Helper\Data;

class CustomerRegisterSuccessObserver implements ObserverInterface
{
    private $cookieManager;
    private $cookieMetadataFactory;
    private $addressHelper;
    private $jsonHelper;
    private $sub2Helper;
    const CUSTOMER_DATA_COOKIE_NAME = "customerData";

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Address $addressHelper,
        Data $sub2Helper,
        JsonHelper $jsonHelper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->addressHelper = $addressHelper;
        $this->sub2Helper = $sub2Helper;
        $this->jsonHelper = $jsonHelper;
    }

    public function execute(Observer $observer)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $params = $observer->getEvent()
            ->getData('account_controller')
            ->getRequest()
            ->getParams();

        $mappedData = $this->addressHelper->mapCustomerData($params);

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
