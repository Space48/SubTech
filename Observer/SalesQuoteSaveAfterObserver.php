<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Space48\SubTech\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Space48\SubTech\Helper\Data;

class SalesQuoteSaveAfterObserver implements ObserverInterface
{

    const BASKET_XML_COOKIE_NAME = "basketXml";
    private $cookieManager;
    private $cookieMetadataFactory;
    private $storeManager;
    private $sub2Helper;

    public function __construct(
        CookieManagerInterface $cookieManager,
        StoreManagerInterface $storeManager,
        Data $sub2Helper,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->storeManager = $storeManager;
        $this->cookieManager = $cookieManager;
        $this->sub2Helper = $sub2Helper;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function execute(Observer $observer)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $quote = $observer->getEvent()->getQuote();
        $basketXml = $this->getBasketXml($quote);

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration(3600)
            ->setPath('/')
            ->setHttpOnly(false);

        $this->cookieManager->setPublicCookie(
            self::BASKET_XML_COOKIE_NAME,
            $basketXml,
            $publicCookieMetadata
        );

        return $this;
    }

    private function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }

    private function getBasketXml($quote)
    {
        $items = $quote->getAllVisibleItems();

        $xml = "<Store>";

        foreach ($items as $item) {
            $xml .= "<Product>";
            $xml .= "<SKU>" . $item->getSku() . "</SKU>";
            $xml .= "<Product_ID>" . $item->getProduct()->getId() . "</Product_ID>";
            $xml .= "<Product_Name>" . $item->getProduct()->getName() . "</Product_Name>";
            $xml .= "<Unit_Price>" . $item->getPriceInclTax() . "</Unit_Price>";
            $xml .= "<Currency>" . $this->getCurrentCurrencyCode() . "</Currency>";
            $xml .= "<Quantity>" . $item->getQty() . "</Quantity>";
            $xml .= "</Product>";
        }

        $xml .= "</Store>";

        return $xml;
    }

    private function getCurrentCurrencyCode()
    {
        return $this->storeManager->getStore()->getCurrentCurrencyCode();
    }
}
