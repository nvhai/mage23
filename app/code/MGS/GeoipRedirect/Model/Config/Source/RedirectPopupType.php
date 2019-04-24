<?php
namespace MGS\GeoIpRedirect\Model\Config\Source;


class RedirectPopupType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Confirmation Popup')], ['value' => 0, 'label' => __('Notification PopUp')]];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Notification PopUp'), 1 => __('Confirmation Popup')];
    }
}