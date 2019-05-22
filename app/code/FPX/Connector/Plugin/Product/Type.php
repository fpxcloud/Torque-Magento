<?php
/**
 * FPX add to cart controller
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Plugin\Product;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Helper\Config;

/**
 * FPX plugin for Product-to-Cart methods
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Type
{
    /**
     * The initial method is triggered when updating quote item.
     * Need to set custom options here to save configuration options.
     *
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string|null $processMode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforePrepareForCartAdvanced(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Magento\Framework\DataObject $buyRequest,
        \Magento\Catalog\Model\Product $product,
        $processMode = null
    ) {
        $subject; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning
        $this->assignConfigurationOptions($product, $buyRequest);
        return [$buyRequest, $product, $processMode];
    }

    /**
     * The initial method is triggered when updating quote item.
     * Need to set custom options here to save configuration options.
     *
     * @param \Magento\Catalog\Model\Product\Type\AbstractType $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    public function aroundProcessConfiguration(
        \Magento\Catalog\Model\Product\Type\AbstractType $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $buyRequest,
        $product,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_LITE
    ) {
        $subject; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning
        $processedProducts = $proceed($buyRequest, $product, $processMode);
        if (is_string($processedProducts)) {
            return $processedProducts;
        }

        foreach ($processedProducts as $product) {
            $this->assignConfigurationOptions($product, $buyRequest);
        }

        return $processedProducts;
    }

    /**
     * Assign configuration options to a product via a custom option
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $buyRequest
     */
    private function assignConfigurationOptions(
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\DataObject $buyRequest
    ) {
        $canConfigure = (bool)$product->getData(Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE);
        $cpqProductId = $product->getCustomOption(ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID);

        $configOptions = $buyRequest->getData(Config::PRODUCT_DATA_PREFIX);
        $additionalOptions = $buyRequest->getData('fpx_options');
        $customPrice = isset($additionalOptions['custom_price'])
            ? $additionalOptions['custom_price']
            : 0;

        if ($canConfigure && is_array($additionalOptions) && !$cpqProductId) {
            // last condition is to skip addToCart initial action

            foreach ($additionalOptions as $code => $value) {
                $product->addCustomOption($code, $value);
            }
            if ($customPrice) {
                $product->setFpxPrice($customPrice);
            }
        }

        $buyRequest->unsetData('fpx_options');
    }
}
