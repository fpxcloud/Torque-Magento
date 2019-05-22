<?php
/**
 * Upgrade Schema for FPX_Connector
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Setup;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * Upgrade Schema for FPX_Connector
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrades Schema for module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.3.0', '<')) {
            $this->createFpxCpqConfigurationTable($setup);
        }
    }

    /**
     * Add fpx_cpq_configurations table.
     *
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    public function createFpxCpqConfigurationTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()
            ->newTable(
                $setup->getTable(ConfigurationInfoInterface::FPX_CPQ_TABLE)
            )
            ->addColumn(
                ConfigurationInfoInterface::FPX_CPQ_COLUMN_ID,
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true, 'unsigned' => true, 'auto_increment' => true],
                'configuration ID'
            )
            ->addColumn(
                ConfigurationInfoInterface::FPX_CPQ_COLUMN_QUOTE_ID,
                Table::TYPE_TEXT,
                255,
                [],
                'CPQ Quote ID'
            )
            ->addColumn(
                ConfigurationInfoInterface::FPX_CPQ_COLUMN_CORRELATION_ID,
                Table::TYPE_TEXT,
                255,
                [],
                'CQP Correlation ID'
            )
            ->addColumn(
                ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID,
                Table::TYPE_TEXT,
                255,
                [],
                'CPQ Product ID'
            )
            ->addColumn(
                ConfigurationInfoInterface::FPX_CPQ_COLUMN_CONFIG_INFO,
                Table::TYPE_TEXT,
                65535,
                [],
                'Configuration info'
            )
            ->addIndex(
                $setup->getIdxName(
                    ConfigurationInfoInterface::FPX_CPQ_TABLE,
                    [ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                [ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID],
                ['type' => AdapterInterface::INDEX_TYPE_INDEX]
            )
            ->setComment('Configuration Info Table');

        $setup->getConnection()->createTable($table);
    }
}
