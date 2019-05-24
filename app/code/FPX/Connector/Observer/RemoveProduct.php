<?php

namespace FPX\Connector\Observer;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use Magento\Framework\Event\ObserverInterface;

class RemoveProduct implements ObserverInterface
{
    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    protected $client;

    public function __construct(
        \FPX\Connector\Helper\FpxProduct $helper,
        \Magento\Customer\Model\Session $customerSession,
        \FPX\Connector\Model\Api\Client $client
    ) {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->client = $client;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();

        if ($this->helper->isFpxEnabledByProductId($product->getId())) {
            // Delete from fpx
            $option = $item->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID);
            if (!$option) {
                return;
            }

            if ($fpxProductId = $option->getValue()) {
//                $this->client->deleteObject($fpxProductId);
            }
        }
    }
}
