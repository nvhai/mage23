<?php
namespace MGS\GeoIp\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class InstallSchema
 * @package MGS\GeoIp\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'mgs_geoip_ipv4'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgs_geoip_ipv4'))
            ->addColumn(
                'geoipv4_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Geoip Ipv4 Id'
            )->addColumn(
                'start_ip_v4',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Start Ip V4'
            )->addColumn(
                'end_ip_v4',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'End Ip V4'
            )->addColumn(
                'geoname_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Geoiname Id'
            )->addColumn(
                'postal_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Postal Code'
            )->addColumn(
                'latitude',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Latitude'
            )->addColumn(
                'longitude',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Longitude'
            )->setComment('MGS Geoip Block IP V4 Table');
        $installer->getConnection()->createTable($table);
         /**
         * Create table 'mgs_geoip_ipv6'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgs_geoip_ipv6'))
            ->addColumn(
                'geoipv6_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Geoip Ipv6 Id'
            )->addColumn(
                'start_ip_v6',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Start Ip V6'
            )->addColumn(
                'end_ip_v6',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'End Ip V6'
            )->addColumn(
                'geoname_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Geoiname Id'
            )->addColumn(
                'postal_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Postal Code'
            )->addColumn(
                'latitude',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Latitude'
            )->addColumn(
                'longitude',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Longitude'
            )->setComment('MGS Geoip IP V6 Table');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'amasty_geoip_location'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgs_geoip_locations'))
            ->addColumn(
                'locations_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Locations Id'
            )
            ->addColumn(
                'geoip_loc_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Geoip Loc Id'
            )
            ->addColumn(
                'country',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'Country'
            )
            ->addColumn(
                'city',
                Table::TYPE_TEXT,
                null,
                ['nullable' => true],
                'City'
            )
            ->setComment('MGS Geoip Location Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}