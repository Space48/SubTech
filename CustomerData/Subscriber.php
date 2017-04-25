<?php

namespace Space48\SubTech\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class Subscriber implements SectionSourceInterface
{
    protected $catalogSession;

    public function __construct(
        \Magento\Catalog\Model\Session $catalogSession
    ) {
        $this->catalogSession = $catalogSession;
    }

    public function getSectionData()
    {
        $subscriberEmail = $this->catalogSession->getData("subscriber_email", true);

        return [
            'subscriber_email' => $subscriberEmail
        ];
    }
}