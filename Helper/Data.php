<?php

namespace Space48\SubTech\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_PATH = 's48_sub2tech/settings/';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
        parent::__construct($context);
    }

    public function isEnabled()
    {

        if ($this->scopeConfig->isSetFlag(self::XML_PATH . "active", ScopeInterface::SCOPE_STORE)
            && $this->getConfig('licence_key') != ""
        ) {
            return true;
        }

        return false;
    }

    public function getConfig($field)
    {
        return $this->scopeConfig->getValue(self::XML_PATH . $field, ScopeInterface::SCOPE_STORE);
    }

    public function getTrackingCode()
    {
        return 'document.write(unescape("%3Cscript src=\'" 
        + document.location.protocol 
        + "//webservices.sub2tech.com/CodeBase/LIVE/Min/sub2.js?LICENSEKEY='
            . $this->getLicenceKey() . '&trackPage=Y\' async=\'true\' type=\'text/javascript\'%3E%3C/script%3E"));
    var __s2tQ = __s2tQ || [];' . "\n";
    }

    public function getLicenceKey()
    {
        return $this->getConfig('licence_key');
    }
}
