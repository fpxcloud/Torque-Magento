<?php
/**
 * Override Wishlist
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\CustomerData;

use FPX\Connector\Helper\FpxProduct;
use Magento\Wishlist\CustomerData\Wishlist as MagentoWishlist;
use Magento\Wishlist\Model\Item;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\ViewInterface;

/**
 * Override Magento CustomerData Wishlist
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Wishlist extends MagentoWishlist
{
    /**
     * @var FpxProduct $fpxProductHelper
     */
    private $fpxProductHelper;

    /**
     * Wishlist constructor.
     *
     * @param Data $wishlistHelper
     * @param Sidebar $block
     * @param ImageFactory $imageHelperFactory
     * @param ViewInterface $view
     * @param FpxProduct $fpxProductHelper
     */
    public function __construct(
        Data $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view,
        FpxProduct $fpxProductHelper
    ) {
        parent::__construct(
            $wishlistHelper,
            $block,
            $imageHelperFactory,
            $view
        );

        $this->fpxProductHelper = $fpxProductHelper;
    }

    /**
     * Override Wishlist getItemData method
     *
     * @param Item $wishlistItem
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getItemData(Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();

        $result = parent::getItemData($wishlistItem);
        $result['fpx_enable'] = $this->fpxProductHelper->isFpxEnabledByProductId($product->getId());

        return $result;
    }
}
