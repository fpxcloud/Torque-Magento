<?php
/**
 * override Magento\Catalog\Block\Product\View
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Block\Product;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Helper\Config;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\Product;

/**
 * Class description
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class View extends Template
{
    /**
     * @var Product
     */
    private $product;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    private $productHelper;

    /**
     * @var \FPX\Connector\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session\Proxy $session,
        \FPX\Connector\Helper\FpxProduct $productHelper,
        \FPX\Connector\Helper\Config $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->session = $session;
        $this->productHelper = $productHelper;
        $this->configHelper = $configHelper;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            /** @var \Magento\Catalog\Block\Product\View $parentBlock */
            $parentBlock = $this->getParentBlock();

            $this->product = $parentBlock->getProduct();
        }

        return $this->product;
    }

    /**
     * Get product SKU
     *
     * @return string
     */
    public function getProductSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Get product FPX ID
     *
     * @return string
     */
    public function getProductFpxId()
    {
        return $this->getProduct()
            ->getCustomAttribute(Config::PRODUCT_ATTRIBUTE_FPX_ID)
            ->getValue();
    }

    /**
     * @param $id
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getQuoteItem($id)
    {
        return $this->session->getQuote()->getItemsCollection()->getItemById($id);
    }

    /**
     * @return mixed|null
     */
    public function getFpxQuoteProductId()
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $this->getQuoteItem($this->getRequest()->getParam('id'));
        $option = $item->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID);

        return $option ? $option->getValue() : null;
    }

    /**
     * Get Iframe URL
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
     * Is FPX enabled for product
     *
     * @return bool
     */
    public function isFpxProduct()
    {
        return $this->productHelper->isFpxEnabledByProductId($this->getProduct()->getId());
    }
}
