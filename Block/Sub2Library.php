<?php

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template;

class Sub2Library extends Template {

    /**
     * SubTech Helper
     *
     * @var \Space48\SubTech\Helper\Data
     */
    protected $sub2Helper = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param GtmHelper $gtmHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Space48\SubTech\Helper\Data $sub2Helper,
        array $data = []
    ) {
        $this->sub2Helper = $sub2Helper;

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

    protected function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return $this->sub2Helper->getTrackingCode();
    }
}