<?php
namespace MGS\GeoIp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


/**
 * Department post mysql resource
 */
class Locations extends AbstractDb
{

    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('mgs_geoip_locations', 'locations_id');
    }


}