<?php
/**
 * Upgrade data
 *
 * @category     FPX
 * @package      FPX_DemoTruckStore
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\DemoTruckStore\Setup;

use FPX\Connector\Helper\Config;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\Data\BlockInterface;
use Magento\Cms\Api\Data\BlockInterfaceFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Store\Model\Store;

/**
 * Upgrade data
 *
 * @category    FPX
 * @package     FPX_DemoTruckStore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**#@+
     * Constants used as data for configuration
     *
     * @var string
     */
    const API_URL          = 'https://hybcpq.fpx.com/';
    const API_VER          = 19;
    const CLIENT_ALIAS     = 'Magento';
    const PROFILE_NAME     = 'magentoProfile';
    const API_KEY          = 'magento_int_835ah';
    /**#@-*/

    /**#@+
     * Constants used as attribute codes
     *
     * @var string
     */
    const STANDARD_FEATURES = 'standard_features';
    /**#@-*/

    /**#@+
     * Constants used as data for product attributes
     *
     * @var string
     */
    const FPX_DATASET_ID = 'd865ebfa21fb0470:-496024c0:14ac0283bfb:-7e5c|0';
    const FPX_ENABLE = 1;
    /**#@-*/

    /**
     * List of products SKU
     *
     * @var array
     */
    protected $productsSku = [
        '359_HD',
        '379_HD',
        '389_HD',
        '579_HD',
        '587_HD',
        '238_Conventional',
        '258_ALP_Conventional',
        '258LP',
        '268_Conventional',
        '268_A',
        '338_Conventional',
        '155_CabOver',
        '155DC_CabOver',
        '195_CabOver',
        '195DC_CabOver',
        '320_Refuse',
        '348_Dump',
        '365_Mixer',
        '114HD_Crane',
        '122MD_Roll_Off',
        '289_SmallDump'
    ];

    /**
     * List of comparable attributes
     *
     * @var array
     */
    protected $comparableAttributes = [
        InstallData::ATTRIBUTE_DRIVER_LOCATION,
        InstallData::ATTRIBUTE_CAB_HEIGHT,
        InstallData::ATTRIBUTE_CAB_WIDTH,
        InstallData::ATTRIBUTE_FRONT_FRAME_HT,
        InstallData::ATTRIBUTE_REAR_FRAME_HT,
        InstallData::ATTRIBUTE_ROAD_CLEARANCE,
        InstallData::ATTRIBUTE_FRONT_TREAD,
        InstallData::ATTRIBUTE_REAR_TREAD,
    ];

    /**
     * @var ConfigInterface $configuration
     */
    protected $configuration;

    /**
     * @var EncryptorInterface $configuration
     */
    protected $encryptor;

    /**
     * @var ProductFactory $productFactory
     */
    protected $productFactory;

    /**
     * @var ProductRepository $productRepository
     */
    protected $productRepository;

    /**
     * @var State $state
     */
    protected $state;

    /**
     * @var PageFactory $pageFactory
     */
    protected $pageFactory;

    /**
     * @var BlockInterfaceFactory $blockInterfaceFactory
     */
    protected $blockInterfaceFactory;

    /**
     * @var BlockRepositoryInterface $blockRepository
     */
    protected $blockRepository;

    /**
     * @var EavSetupFactory $eavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var PageRepositoryInterface $pageRepository
     */
    protected $pageRepository;

    /**
     * UpgradeData constructor.
     *
     * @param ConfigInterface $configuration
     * @param EncryptorInterface $encryptor
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param State $state
     * @param PageFactory $pageFactory
     * @param BlockInterfaceFactory $blockInterfaceFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param EavSetupFactory $eavSetupFactory
     * @param PageRepositoryInterface $pageRepository
     * @return void
     */
    public function __construct(
        ConfigInterface $configuration,
        EncryptorInterface $encryptor,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        State $state,
        PageFactory $pageFactory,
        BlockInterfaceFactory $blockInterfaceFactory,
        BlockRepositoryInterface $blockRepository,
        EavSetupFactory $eavSetupFactory,
        PageRepositoryInterface $pageRepository
    ) {
        $this->configuration = $configuration;
        $this->encryptor = $encryptor;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->state = $state;
        $this->pageFactory = $pageFactory;
        $this->blockInterfaceFactory = $blockInterfaceFactory;
        $this->blockRepository = $blockRepository;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->pageRepository = $pageRepository;
    }

    /**
     * FPX_DemoTruckStore upgrade
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            $this->setFpxConfigurationValues();
            $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'setFpxAttrValuesToProduct']);
        }
        if (version_compare($context->getVersion(), '0.3.0', '<')) {
            $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'updateHomePage']);
            $this->createFooterBlock();
        }
        if (version_compare($context->getVersion(), '0.4.0', '<')) {
            $this->addFeaturesAttribute($setup);
        }
        if (version_compare($context->getVersion(), '0.5.0', '<')) {
            $this->state->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'updateHomePage']);
        }
        if (version_compare($context->getVersion(), '0.6.0', '<')) {
            $this->setAttributesAsComparable($setup);
        }
    }

    /**
     * set FPX configuration values for Demo store
     *
     * @return void
     */
    public function setFpxConfigurationValues()
    {
        $this->configuration
            ->saveConfig(
                Config::XML_PATH_API_URL,
                self::API_URL,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            )
            ->saveConfig(
                Config::XML_PATH_API_VER,
                self::API_VER,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            )
            ->saveConfig(
                Config::XML_PATH_CLIENT_ALIAS,
                self::CLIENT_ALIAS,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            )
            ->saveConfig(
                Config::XML_PATH_PROFILE_NAME,
                self::PROFILE_NAME,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            )
            ->saveConfig(
                Config::XML_PATH_API_KEY,
                $this->encryptor->encrypt(self::API_KEY),
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
    }


    /**
     * set FPX product attribute values for Demo products
     *
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setFpxAttrValuesToProduct()
    {
        $products = $this->productFactory
            ->create()
            ->getCollection()
            ->addAttributeToFilter('sku', ['in' => $this->productsSku]);

        /** @var Product $product */
        foreach ($products as $product) {
            $product->setCustomAttributes([
                Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE => self::FPX_ENABLE,
                Config::PRODUCT_ATTRIBUTE_FPX_ID => self::FPX_DATASET_ID
            ]);

            $this->productRepository->save($product);
        }
    }

    /**
     * Update Home Page content
     *
     * @throws \Exception
     */
    public function updateHomePage()
    {
        try {
            $productId = $this->productRepository->get('155_CabOver')->getId();
        } catch (NoSuchEntityException $e) {
            $productId = null;
        }

        /** @var Page $cmsPage */
        $cmsPage = $this->pageFactory->create();
        $cmsPageId = $cmsPage->checkIdentifier('home', Store::DEFAULT_STORE_ID);
        $cmsPage = $this->pageRepository->getById($cmsPageId);
        $cmsPage->setContent($this->getHomePageContent($productId));
        $this->pageRepository->save($cmsPage);
    }

    /**
     * Create CMS Block
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createFooterBlock()
    {
        /** @var BlockInterface $cmsBlock */
        $cmsBlock = $this->blockInterfaceFactory->create();
        $cmsBlock->setIdentifier('footer-socials')
            ->setTitle('Footer Socials')
            ->setContent($this->getFooterBlockContent())
            ->setData('stores', [1]);

        $this->blockRepository->save($cmsBlock);
    }

    /**
     * Add Standard Features attribute
     *
     * @param ModuleDataSetupInterface $setup
     */
    public function addFeaturesAttribute(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            self::STANDARD_FEATURES,
            [
                'label' => 'Standard Features',
                'type' => 'text',
                'input' => 'textarea',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'searchable' => true,
                'comparable' => true,
                'wysiwyg_enabled' => true,
                'is_html_allowed_on_front' => true,
                'visible_in_advanced_search' => true
            ]
        );
    }

    /**
     * Set some attributes as comparable
     *
     * @param ModuleDataSetupInterface $setup
     */
    public function setAttributesAsComparable(ModuleDataSetupInterface $setup)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        foreach ($this->comparableAttributes as $attribute) {
            $eavSetup->updateAttribute(
                Product::ENTITY,
                $attribute,
                'is_comparable',
                1
            );
        }
    }

    /**
     * get content for Home CMS page
     *
     * @param int $productId
     * @return string
     */
    public function getHomePageContent($productId = null)
    {

        return <<<HTML
            <div class="banner"><img src="{{media url="wysiwyg/fpx/banner.jpg"}}" alt="" /></div>
            <div class="driving-force">
            <h2>The Driving Force</h2>
            <ul>
            <li><img src="{{media url="wysiwyg/fpx/ic1.png"}}" alt="" />
            <h2>INCREASED UP TIME</h2>
            <p>Over 100 years&rsquo; experience building vocational trucks has resulted in a truck designed to be on the road and at the worksite, not in the service bay.</p>
            </li>
            <li><img src="{{media url="wysiwyg/fpx/ic2.png"}}" alt="" />
            <h2>Drivers First</h2>
            <p>Putting the driver at the forefront means we designed the HV&trade; Series around the people who use it, for maximum safety and comfort.</p>
            </li>
            <li><img src="{{media url="wysiwyg/fpx/ic3.png"}}" alt="" />
            <h2>INTEGRATION</h2>
            <p>Our advanced Diamond Logic&reg; Electrical System streamlines truck chassis and body equipment integration and performance.</p>
            </li>
            </ul>
            </div>
            <div class="cab-section"><img src="{{media url="wysiwyg/fpx/im1.png"}}" alt="" />
            <div class="content">
            <h2>155 Cab Over</h2>
            <p>Ready for a variety of truck and tractor applications, the crew cab configuration offers the flexibility you need, combined with the Freightliner strength and reliability you can count on</p>
            <div class="learn-more">{{widget type="Magento\Catalog\Block\Product\Widget\Link" anchor_text="Learn More" template="product/widget/link/link_inline.phtml" id_path="product/{$productId}"}}</div>
            </div>
            </div>
HTML;
    }

    /**
     * Get content for CMS Block
     *
     * @return string
     */
    public function getFooterBlockContent()
    {
        return <<<HTML
        <div class="footer-socilas">
        <h2>Follow Us</h2>
        <ul>
        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
        <li><a href="#"><i class="fa fa-instagram"></i></a></li>
        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
        </ul>
        </div>
HTML;
    }
}
