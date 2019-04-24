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
class InstallSchema extends InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'amasty_geoip_block'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('mgs_geoip_blocks_ipv4'))
            ->addColumn(
                'block_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Block Id'
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
            )->addColumn(
                'accuracy_radius',
                Table::TYPE_FLOAT,
                null,
                ['nullable' => true],
                'Accuracy Radius'
            )->addIndex(
                $installer->getIdxName('mgs_geoip_blocks_ipv4', ['start_ip_v4']),
                ['start_ip_v4']
            )->addIndex(
                $setup->getIdxName(
                    $installer->getTable('mgs_geoip_blocks_ipv4'),
                    ['title', 'url_key', 'short_content', 'content', 'meta_keywords', 'meta_description', 'tags'],
                    AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['title', 'url_key', 'short_content', 'content', 'meta_keywords', 'meta_description', 'tags'],
                ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
            )

            ->setComment('MGS Geoip Block IP V4 Table');

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'amasty_geoip_location'
         */

        $table = $installer->getConnection()
            ->newTable($installer->getTable('amasty_geoip_location'))
            ->addColumn(
                'location_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Location Id'
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
            ->setComment('Amasty Geoip Location Table')
            ->setOption('type', 'MyISAM');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}