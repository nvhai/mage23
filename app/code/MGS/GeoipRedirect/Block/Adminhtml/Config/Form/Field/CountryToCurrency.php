<?php

namespace MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field;

class CountryToCurrency extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * @var \MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\CountryList
     */
    protected $countryRenderer = null;

    /**
     * @var \MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\Currency
     */
    protected $currencyRenderer = null;

    public function _prepareToRender()
    {
        $this->addColumn(
            'country_to_currency_switch',
            [
                'label'     => __('Country'),
                'renderer'  => $this->getCountryRendererList(),
            ]
        );
        $this->addColumn('currency',
            [
                'label' => __('Currency'),
                'renderer'  => $this->getCurrencyRenderer()
            ]
        );
        $this->_addAfter = false;
    }

    /**
     * Returns renderer for country element
     *
     * @return \MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\CountryList
     */
    public function getCountryRendererList()
    {
        if (!$this->countryRenderer) {
            $this->countryRenderer = $this->getLayout()->createBlock(
                '\MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\CountryList',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->countryRenderer;
    }

    /**
     * @return \MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\Currency
     */
    public function getCurrencyRenderer()
    {
        if (!$this->currencyRenderer) {
            $this->currencyRenderer = $this->getLayout()->createBlock(
                '\MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\Currency',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->currencyRenderer;
    }

    public function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $countries = $row->getCountryToCurrencySwitch();
        $options = [];
        if ($countries) {
            $countries = explode(',', $countries);
            foreach ($countries as $country) {
                $options['option_' . $this->getCountryRendererList()->calcOptionHash($country)]
                    = 'selected="selected"';
            }

            $currency = $row->getCurrency();

            $options['option_' . $this->getCurrencyRenderer()->calcOptionHash($currency)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
        return;
    }
}
