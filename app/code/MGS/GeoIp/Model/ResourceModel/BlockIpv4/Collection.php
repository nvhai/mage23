<?php
namespace MGS\GeoIp\Model\ResourceModel\BlockIpv4;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = \MGS\GeoIp\Model\BlockIpv4::GEOIPV4_ID;
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
        $this->_init('MGS\GeoIp\Model\BlockIpv4', 'MGS\GeoIp\Model\ResourceModel\BlockIpv4');
    }

}