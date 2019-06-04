<?php


namespace MGS\GeoIp\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MGS\GeoIp\Helper\Data as HelperData;

class ImportGeoIp extends Field
{
    /**
     * @var string
     */
    protected $_template = 'MGS_GeoIp::system/config/import_geoip.phtml';

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * DownloadGeoIp constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        array $data = []
    )
    {
        $this->_helperData = $helperData;

        parent::__construct($context, $data);
    }

    /**
     * Remove scope label
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();

        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mgs_geoip/system_config/importgeoip');
    }
    public function getDownloadAjaxUrl()
    {
        return $this->getUrl('mgs_geoip/system_config/downloadgeoip');
    }
    /**
     * Generate collect button html
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
            ->setData([
                'id'    => 'import_geoip',
                'label' => __('Download and Import GeoIp'),
            ]);

        return $button->toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function isDisplayIcon()
    {
        return $this->_helperData->getAddressHelper()->checkHasLibrary() ? '' : 'hidden="hidden';
    }
}
