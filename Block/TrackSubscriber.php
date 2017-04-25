<?php

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template;

class TrackSubscriber extends Template {

    const COOKIE_NAME = 'subscriberData';

    /**
     * SubTech Helper
     *
     * @var \Space48\SubTech\Helper\Data
     */
    protected $sub2Helper = null;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param GtmHelper $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Space48\SubTech\Helper\Data $sub2Helper,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->sub2Helper = $sub2Helper;
        $this->_cookieManager = $cookieManager;

        parent::__construct(
            $context,
            $data
        );
    }

    public function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }

    public function getCookie()
    {
        return $this->_cookieManager->getCookie(self::COOKIE_NAME);
    }

    protected function _toHtml()
    {
        if (!$this->isEnabled() || !empty($this->getCookie())) {
            return '';
        }

        $cookieData = $this->getCookie();
        //$this->_cookieManager->deleteCookie(self::COOKIE_NAME);

        $jsonArray = array(
            'Email' => $cookieData
        );

        /*$html = "<script type=\"text/x-magento-init\">
{
    \"*\": {
        \"subscriberTracking\": {}
    }
}
</script>";*/

        return "";
    }
}