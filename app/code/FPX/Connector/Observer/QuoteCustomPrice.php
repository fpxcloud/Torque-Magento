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
class QuoteCustomPrice implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item[] $items */
        $items = $observer->getItems();

        foreach ($items as $item) {
            if (!$item->getProduct()) {
                continue;
            }

            if ($fpxPrice = $item->getProduct()->getFpxPrice()) {
                $item->setCustomPrice($fpxPrice)->setOriginalCustomPrice($fpxPrice);
            }
        }
    }
}
