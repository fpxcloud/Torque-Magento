<?php
/**
 * FpxProduct Helper
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;
use Magento\Store\Model\Store;

/**
 * FpxProduct Helper
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class FpxProduct extends AbstractHelper
{
    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var ResourceProduct $resourceProduct
     */
    private $resourceProduct;

    /**
     * FpxProduct constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param ResourceProduct $resourceProduct
     */
    public function __construct(
        Context $context,
        Config $config,
        ResourceProduct $resourceProduct
    ) {
        parent::__construct($context);

        $this->config = $config;
        $this->resourceProduct = $resourceProduct;
    }

    /**
     * Check that the product is configured on FPX
     *
     * @param $productId
     * @return bool
     */
    public function isFpxEnabledByProductId($productId)
    {
        return $this->config->isFpxModuleEnable()
            && $this->isFpxLoadEnabled($productId)
            && $this->hasFpxDataset($productId);
    }

    /**
     * Check whether FpxLoad attribute is enabled
     *
     * @param int|string $productId
     * @return bool
     */
    public function isFpxLoadEnabled($productId)
    {
        return $this->resourceProduct->getAttributeRawValue(
            $productId,
            Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE,
            Store::DEFAULT_STORE_ID
        );
    }

    /**
     * Check whether fpx_id attribute is not null in the product
     *
     * @param int|string $productId
     * @return bool
     */
    public function hasFpxDataset($productId)
    {
        $fpxId = $this->resourceProduct->getAttributeRawValue(
            $productId,
            Config::PRODUCT_ATTRIBUTE_FPX_ID,
            Store::DEFAULT_STORE_ID
        );

        return $fpxId != null;
    }
}
