<?php
/**
 * Override Quote item price by FPX configured product price
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

/**
 * Override Quote item price by FPX configured product price
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class CartCustomPrice implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $observer->getQuoteItem();

        if (!$quoteItem->getProduct()) {
            return;
        }

        if ($fpxPrice = $quoteItem->getProduct()->getFpxPrice()) {
            $quoteItem->setCustomPrice($fpxPrice)->setOriginalCustomPrice($fpxPrice);
        }
    }
}
