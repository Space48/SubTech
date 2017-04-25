<?php

namespace Space48\SubTech\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH = 's48_sub2tech/settings/';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    public function isEnabled() {

        if ($this->scopeConfig->isSetFlag(self::XML_PATH."active", \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        && $this->getConfig('licence_key') != "") {
            return true;
        }

        return false;
    }

    public function getConfig($field) {
        return $this->scopeConfig->getValue(self::XML_PATH.$field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getLicenceKey()
    {
        return $this->getConfig('licence_key');
    }

    public function getTrackingCode()
    {
        return 'document.write(unescape("%3Cscript src=\'" + document.location.protocol + "//webservices.sub2tech.com/CodeBase/LIVE/Min/sub2.js?LICENSEKEY='. $this->getLicenceKey() . '&trackPage=Y\' async=\'true\' type=\'text/javascript\'%3E%3C/script%3E"));
    var __s2tQ = __s2tQ || [];'."\n";
    }

}