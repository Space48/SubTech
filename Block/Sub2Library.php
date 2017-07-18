<?php

namespace Space48\SubTech\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Space48\SubTech\Helper\Data;

class Sub2Library extends Template
{

    /**
     * SubTech Helper
     *
     * @var Data
     */
    private $sub2Helper = null;

    /**
     * @param Context $context
     * @param Data    $sub2Helper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Data $sub2Helper,
        array $data = []
    ) {
        $this->sub2Helper = $sub2Helper;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->isEnabled()) {
            return '';
        }

        return $this->sub2Helper->getTrackingCode();
    }

    /**
     * @return bool
     */
    private function isEnabled()
    {
        return $this->sub2Helper->isEnabled()
            ? true : false;
    }
}
