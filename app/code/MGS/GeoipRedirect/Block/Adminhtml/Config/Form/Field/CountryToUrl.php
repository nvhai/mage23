<?php

namespace MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field;

class CountryToUrl extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
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
            'redirect_to_website_outside',
            [
                'label'     => __('Country'),
                'renderer'  => $this->getCountryRenderer(),
            ]
        );
        $this->addColumn('country_url_redirect',
            [
                'label' => __('URL'),
            ]
        );
        $this->_addAfter = false;
    }

    /**
     * Returns renderer for country element
     *
     * @return \MGS\GeoIpRedirect\Block\Adminhtml\Config\Form\Field\CountryList
     */
    public function getCountryRenderer()
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

    public function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $countries = $row->getRedirectToWebsiteOutside();
        $options = [];
        if ($countries) {
            $countries = explode(',', $countries);
            foreach ($countries as $country) {
                $options['option_' . $this->getCountryRenderer()->calcOptionHash($country)]
                    = 'selected="selected"';
            }

        }
        $row->setData('option_extra_attrs', $options);
        return;
    }
}
