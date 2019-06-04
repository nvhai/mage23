<?php
namespace MGS\GeoIp\Model;

use \Magento\Framework\Model\AbstractModel;


class Locations extends AbstractModel
{
    const LOCATION_ID = 'locations_id'; // We define the id fieldname
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'geoip'; // parent value is 'core_abstract'

    /**
     * Name of object id field
     *
     * @var string
     */
    protected $_idFieldName = self::LOCATION_ID;


    protected function _construct()
    {
        $this->_init('MGS\GeoIp\Model\ResourceModel\Locations');
    }

}

