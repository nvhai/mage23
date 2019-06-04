<?php
namespace MGS\GeoIp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;


/**
 * Department post mysql resource
 */
class BlockIpv4 extends AbstractDb
{

    protected function _construct()
    {
        // Table Name and Primary Key column
        $this->_init('mgs_geoip_ipv4', 'geoipv4_id');
    }


}