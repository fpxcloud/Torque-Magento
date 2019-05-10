<?php
/**
 * override Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Block\Cart\Item\Renderer\Actions;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Helper\Config;

/**
 * Cart Item renderer generic action
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Generic extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    const URL_PATH_AUTHENTICATE = 'fpx_connector/fpx/authenticate';

    /**
     * @var Config
     */
    private $configHelper;

    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    private $productHelper;

    /**
     * Generic constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \FPX\Connector\Helper\Config                     $configHelper
     * @param \FPX\Connector\Helper\FpxProduct                 $productHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \FPX\Connector\Helper\Config $configHelper,
        \FPX\Connector\Helper\FpxProduct $productHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->productHelper = $productHelper;
    }

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getProductSku()
    {
        $option = $this->getItem()->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID);
        return $option ? $option->getValue() : null;
    }

    /**
     * Get product FPX ID
     *
     * @return string
     */
    public function getProductFpxId()
    {
        $option = $this->getItem()->getOptionByCode(Config::PRODUCT_ATTRIBUTE_FPX_ID);
        return $option ? $option->getValue() : null;
    }

    /**
     * Get Correlation ID
     *
     * @return string
     */
    public function getCorrelationId()
    {
        $option = $this->getItem()->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_CORRELATION_ID);
        return $option ? $option->getValue() : null;
    }

    /**
     * Get product URL for iframe
     *
     * @return string
     */
    public function getIframeUrl()
    {
        return $this->configHelper->getProductUrlForIframe();
    }

    /**
     * Get product data prefix
     *
     * @return string
     */
    public function getProductDataPrefix()
    {
        return $this->configHelper->getProductDataPrefix();
    }

    /**
     * FPX is enabled for product
     *
     * @return bool
     */
    public function isFpxProduct()
    {
        return $this->isProductVisibleInSiteVisibility()
            && $this->productHelper->isFpxEnabledByProductId($this->getItem()->getProduct()->getId());
    }

    /**
     * get authenticate URL
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->getUrl(self::URL_PATH_AUTHENTICATE, [
            'action' => 'edit'
        ]);
    }
}
