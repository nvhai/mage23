<?php


namespace MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field;
use Magento\Framework\View\Element\Context;
use Magento\Directory\Model\Config\Source\Country ;

class CountryList extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    public $country;

    /**
     * CountryConfig constructor.
     * @param Context $context
     * @param Country $country
     * @param array $data
     */
    public function __construct(
        Context $context,
        Country $country,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->country = $country;
    }


    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $this->setExtraParams('multiple="multiple"');
        $this->setOptions($this->country->toOptionArray(true));

        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value . '[]');
    }
}
