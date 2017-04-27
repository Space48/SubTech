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
    const CUSTOMER_DATA_COOKIE_NAME = "customerData";

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getData('model');
        $mergedData = array_merge($customer->getData(), $this->getCustomerAddressData($customer));
        $mappedData = $this->mapCustomerData($mergedData);

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

    public function getDefaultAddressId($customer)
    {
        if ($customer->getDefaultShipping()) {
            return $customer->getDefaultShipping();
        } elseif ($customer->getDefaultBilling()) {
            return $customer->getDefaultBilling();
        }

        return false;
    }

    public function getCustomerAddressData($customer)
    {
        $defaultAddressId = $this->getDefaultAddressId($customer);
        $addresses = $customer->getAddresses();
        $addressDataArray = [];

        if (count($addresses) == 0) {
            return [];
        }

        foreach ($addresses as $address) {

            if ($address->getEntityId() == $defaultAddressId) {
                return $address->getData();
            }

            $addressDataArray[] = $address->getData();
        }

        return $addressDataArray[0];
    }

    public function mapCustomerData($data)
    {
        $mappedData = [];

        $mappedData['Forename'] = isset($data['firstname']) ? $data['firstname'] : "";
        $mappedData['Surname'] = isset($data['lastname']) ? $data['lastname'] : "";
        $mappedData['Email'] = isset($data['email']) ? $data['email'] : "";
        $mappedData['Address1'] = isset($data['street']) ? $data['street'] : "";
        $mappedData['Address2'] = isset($data['city']) ? $data['city'] : "";
        $mappedData['Address3'] = isset($data['region']) ? $data['region'] : "";
        $mappedData['Postcode'] = isset($data['postcode']) ? $data['postcode'] : "";
        $mappedData['Landline'] = isset($data['telephone']) ? $data['telephone'] : "";
        $mappedData['Mobile'] = isset($data['mobile']) ? $data['mobile'] : "";

        return array_filter($mappedData);

    }
}
