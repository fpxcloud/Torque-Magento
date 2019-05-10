<?php
/**
 * Upgrade data
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Setup;

use FPX\Connector\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Upgrade data
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @return void
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Method upgrade.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            $this->createConnectorProductAttributes($setup);
        }
    }

    /**
     * Create Connector product attributes.
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function createConnectorProductAttributes(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::PRODUCT_ATTRIBUTE_FPX_ID,
            [
                'label' => 'FPX Dataset ID',
                'type' => 'varchar',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'sort_order' => 15
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE,
            [
                'label' => 'Load into FPX',
                'type' => 'int',
                'input' => 'select',
                'source' => Boolean::class,
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'default' => 0,
                'visible' => true,
                'sort_order' => 16
            ]
        );
    }
}
