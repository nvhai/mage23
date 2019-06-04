<?php
namespace MGS\GeoIp\Model\ResourceModel\Locations;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = \MGS\GeoIp\Model\Locations::LOCATION_ID;
    /**
     * @var array
     */
    protected $_joinedFields = [];
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MGS\GeoIp\Model\Locations', 'MGS\GeoIp\Model\ResourceModel\Locations');
    }

}