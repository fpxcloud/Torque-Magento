<?php
/**
 * FPX_DemoTruckStore Install Data
 *
 * @category     FPX
 * @package      FPX_DemoTruckStore
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\DemoTruckStore\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * FPX_DemoTruckStore Install Data
 *
 * @category FPX
 * @package  FPX_DemoTruckStore
 */
class InstallData implements InstallDataInterface
{
    /**#@+
     * Constants used as attribute codes
     *
     * @var string
     */
    const ATTRIBUTE_COST                = 'cost';
    const ATTRIBUTE_DEALER_NET_PRICE    = 'dealer_net_price';
    const ATTRIBUTE_DRIVER_LOCATION     = 'driver_location';
    const ATTRIBUTE_CAB_HEIGHT          = 'cab_height';
    const ATTRIBUTE_CAB_WIDTH           = 'cab_width';
    const ATTRIBUTE_FRONT_FRAME_HT      = 'front_frame_ht';
    const ATTRIBUTE_REAR_FRAME_HT       = 'rear_frame_ht';
    const ATTRIBUTE_ROAD_CLEARANCE      = 'road_clearance';
    const ATTRIBUTE_FRONT_TREAD         = 'front_tread';
    const ATTRIBUTE_REAR_TREAD          = 'rear_tread';
    /**#@-*/

    /** @var EavSetupFactory */
    protected $eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Function install
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_COST,
            [
                'label' => 'Cost',
                'type' => 'decimal',
                'input' => 'price',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'used_for_promo_rules' => true
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_DEALER_NET_PRICE,
            [
                'label' => 'Dealer Net Price',
                'type' => 'decimal',
                'input' => 'price',
                'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'used_for_promo_rules' => true
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_DRIVER_LOCATION,
            [
                'label' => 'Driver Location',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_CAB_HEIGHT,
            [
                'label' => 'Cabin Height',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_CAB_WIDTH,
            [
                'label' => 'Cabin Width',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_FRONT_FRAME_HT,
            [
                'label' => 'Front Frame Height',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_REAR_FRAME_HT,
            [
                'label' => 'Rear Frame Height',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_ROAD_CLEARANCE,
            [
                'label' => 'Road Clearance',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_FRONT_TREAD,
            [
                'label' => 'Front Tread',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::ATTRIBUTE_REAR_TREAD,
            [
                'label' => 'Rear Tread',
                'type' => 'decimal',
                'input' => 'text',
                'required' => false,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General',
                'visible' => true,
                'frontend_class' => 'validate-number'
            ]
        );
    }
}
