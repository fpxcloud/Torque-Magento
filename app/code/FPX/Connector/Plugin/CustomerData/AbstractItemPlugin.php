<?php

namespace FPX\Connector\Plugin\CustomerData;

use Magento\Checkout\CustomerData\DefaultItem;
use Magento\Quote\Model\Quote\Item;

class AbstractItemPlugin
{
    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    private $helper;

    public function __construct(
        \FPX\Connector\Helper\FpxProduct $helper
    ) {
        $this->helper = $helper;
    }

    public function aroundGetItemData(DefaultItem $subject, callable $proceed, Item $item)
    {
        $subject; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning

        $data = $proceed($item);
        $data['fpx_enabled'] = $this->helper->isFpxEnabledByProductId($item->getProduct()->getId());

        return $data;
    }
}
