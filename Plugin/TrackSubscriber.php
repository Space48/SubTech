<?php
namespace Space48\SubTech\Plugin;

class TrackSubscriber
{
    protected $catalogSession;
    protected $cookieManager;
    protected $cookieMetadataFactory;

    public function __construct(
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->catalogSession = $catalogSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function afterSubscribe(\Magento\Newsletter\Model\Subscriber $subscriber)
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

            $this->cookieManager->setPublicCookie("subscriberEmail",
                json_encode($mappedData),
                $publicCookieMetadata
            );
        }
    }
}