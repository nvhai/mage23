<?php
namespace MGS\GeoIpRedirect\Model\Config\Source;


class ApplyToLogic implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('All Except Specified URLs')], ['value' => 2, 'label' => __('Specified URLs')], ['value' => 3, 'label' => __('Redirect From Home Page Only')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [1 => __('All Except Specified URLs'), 2 => __('Specified URLs'), 3 => __('Redirect From Home Page Only')];
    }
}