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
    protected $_storeManager;
    protected $sub2Helper;
    const BASKET_XML_COOKIE_NAME = "basketXml";

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Space48\SubTech\Helper\Data $sub2Helper,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    )
    {
        $this->_storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->cookieManager = $cookieManager;
        $this->sub2Helper = $sub2Helper;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
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

    public function getBasketXml($quote) {

        $items = $quote->getAllVisibleItems();

        $xml = "<Store>";

        foreach ($items as $item) {

            $xml .= "<Product>";
            $xml .= "<SKU>".$item->getSku()."</SKU>";
            $xml .= "<Product_ID>".$item->getProduct()->getId()."</Product_ID>";
            $xml .= "<Product_Name>".$item->getProduct()->getName()."</Product_Name>";
            $xml .= "<Unit_Price>".$item->getPriceInclTax()."</Unit_Price>";
            $xml .= "<Currency>".$this->getCurrentCurrencyCode()."</Currency>";
            $xml .= "<Quantity>".$item->getQty()."</Quantity>";
            $xml .= "</Product>";
        }

        $xml .= "</Store>";

        return $xml;

    }

    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

    public function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }
}
