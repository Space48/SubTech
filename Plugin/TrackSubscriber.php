<?php

namespace Space48\SubTech\Plugin;

use Magento\Catalog\Model\Session;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Newsletter\Model\Subscriber;

class TrackSubscriber
{

    private $catalogSession;
    private $cookieManager;
    private $cookieMetadataFactory;
    private $jsonHelper;

    public function __construct(
        Session $catalogSession,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        Data $jsonHelper
    ) {
        $this->catalogSession = $catalogSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->jsonHelper = $jsonHelper;
    }

    public function afterSubscribe(Subscriber $subscriber)
    {
        $subscriberData = $subscriber->getData();

        if ($subscriber->getStatus()) {
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(3600)
                ->setPath('/')
                ->setHttpOnly(false);

            $mappedData = [
                'Email' => $subscriberData['subscriber_email']
            ];

            $this->cookieManager->setPublicCookie(
                "subscriberEmail",
                $this->jsonHelper->jsonEncode($mappedData),
                $publicCookieMetadata
            );
        }
    }
}
