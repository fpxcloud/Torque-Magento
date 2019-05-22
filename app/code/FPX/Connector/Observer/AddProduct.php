<?php

namespace FPX\Connector\Observer;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Helper\Config;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;

class AddProduct implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    protected $helper;

    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    protected $client;

    /**
     * @var \FPX\Connector\Model\ConfigurationManagement
     */
    protected $configurationManagement;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        \FPX\Connector\Helper\FpxProduct $helper,
        \FPX\Connector\Model\Api\Client $client,
        \FPX\Connector\Model\ConfigurationManagement $configurationManagement
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->client = $client;
        $this->configurationManagement = $configurationManagement;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item[] $items */
        $items = $observer->getItems();

        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getQuoteItem();

//        foreach ($items as $item) {
            if (!$this->isFpxActionRequired($item->getProduct())) {
                return;
            }

            try {
                $configurationInfo = $this->configurationManagement->create($this->request->getParam(Config::PRODUCT_DATA_PREFIX));
            } catch (CouldNotSaveException $e) {
                $this->logger->error($e->getMessage());

                return;
            }

            if ($configurationInfo->getPrice() !== null) {
                $item->setCustomPrice($configurationInfo->getPrice());
                $item->getQuote()->setTriggerRecollect(true);
            }
//        }
    }

    protected function isFpxActionRequired(\Magento\Catalog\Model\Product $product)
    {
        return $this->helper->isFpxEnabledByProductId($product->getId())
            && !empty($this->request->getParam(Config::PRODUCT_DATA_PREFIX));
    }
}
