<?php


namespace MGS\GeoIp\Block\Adminhtml\Config;

class ConfigTemplate extends \Magento\Backend\Block\Template
{
    /**
     * @var \Amasty\Geoip\Helper\Data
     */
    public $geoipHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context
    ) {
        parent::__construct($context);
    }



}
