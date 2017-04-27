<?php

namespace Space48\SubTech\Helper;

class Address extends \Magento\Framework\App\Helper\AbstractHelper {

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
        $mappedData['Optout1P'] = "";
        $mappedData['Optout3P'] = "";

        return array_filter($mappedData);
    }
}