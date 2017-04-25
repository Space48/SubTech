<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Space48\SubTech\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteSaveAfterObserver implements ObserverInterface
{
    protected $checkoutSession;
    protected $cookieManager;
    protected $cookieMetadataFactory;
    const BASKET_XML_COOKIE_NAME = "basketXml";

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
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
    }

    public function getBasketXml($quote) {

        $items = $quote->getAllVisibleItems();

        $xml = "<Store>";

        foreach ($items as $item) {

            $xml .= "<Product>";
            $xml .= "<SKU>".$item->getSku()."</SKU>";
            $xml .= "</Product>";

        }

        $xml .= "</Store>";

        return $xml;

    }
}