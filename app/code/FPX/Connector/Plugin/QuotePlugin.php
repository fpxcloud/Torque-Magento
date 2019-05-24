<?php

namespace FPX\Connector\Plugin;

use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Helper\Config;
use Magento\Framework\Exception\CouldNotSaveException;

class QuotePlugin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \FPX\Connector\Helper\FpxProduct
     */
    private $helper;

    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    private $client;

    /**
     * @var \FPX\Connector\Model\ConfigurationManagement
     */
    private $configurationManagement;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    private $objectFactory;

    public function __construct(
        \Magento\Customer\Model\Session\Proxy $customerSession,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        \FPX\Connector\Helper\FpxProduct $helper,
        \FPX\Connector\Model\Api\Client $client,
        \FPX\Connector\Model\ConfigurationManagement $configurationManagement,
        \Magento\Framework\DataObject\Factory $objectFactory
    ) {
        $this->customerSession = $customerSession;
        $this->request = $request;
        $this->logger = $logger;
        $this->helper = $helper;
        $this->client = $client;
        $this->configurationManagement = $configurationManagement;
        $this->objectFactory = $objectFactory;
    }

    /**
     * @param \Magento\Quote\Model\Quote     $quote
     * @param \Magento\Catalog\Model\Product $product
     * @param null                           $request
     * @param                                $processMode
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeAddProduct(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        $quote; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning

        $options = $this->request->getParam(Config::PRODUCT_DATA_PREFIX);
        if (empty($options)) {
            return null;
        }

        if ($this->request->getParam(Config::PRODUCT_DATA_PREFIX . 'old_config')) {
            return null;
        }

        if (!$this->helper->isFpxEnabledByProductId($product->getId())) {
            return null;
        }

        if ($request === null) {
            $request = $this->objectFactory->create();
        }

        $request->setQty(1); // Force quantity to 1, because FPX allows only configured products with quantity equal 1

        if (!$this->customerSession->getFpxQuoteId()) {
            $this->customerSession->setFpxQuoteId($options['quoteId']);
        }
        if (!$this->customerSession->getFpxCorrelationId()) {
            $this->customerSession->setFpxCorrelationId($options['correlationId']);
        }

        try {
            $configurationInfo = $this->configurationManagement->create($options);
        } catch (CouldNotSaveException $e) {
            $this->logger->error($e->getMessage());

            return null;
        }

        if ($configurationInfo->getPrice() !== null) {
            $product
                ->addCustomOption(
                    ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID,
                    $configurationInfo->getCpqProductId()
                )->addCustomOption(
                    ConfigurationInfoInterface::FPX_CPQ_COLUMN_ID,
                    $configurationInfo->getId()
                )->addCustomOption(
                    Config::PRODUCT_ATTRIBUTE_FPX_ID,
                    $product->getCustomAttribute(Config::PRODUCT_ATTRIBUTE_FPX_ID)->getValue()
                )->addCustomOption(
                    Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE,
                    $product->getCustomAttribute(Config::PRODUCT_ATTRIBUTE_FPX_LOAD_ENABLE)->getValue()
                )->addCustomOption(
                    ConfigurationInfoInterface::FPX_CPQ_COLUMN_CORRELATION_ID,
                    $this->customerSession->getFpxCorrelationId()
                )->setFpxPrice(
                    $configurationInfo->getPrice()
                );
        }

        return [$product, $request, $processMode];
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param                            $itemId
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeRemoveItem(\Magento\Quote\Model\Quote $quote, $itemId)
    {
        if (!empty($this->request->getParam(Config::PRODUCT_DATA_PREFIX))) {
            return;
        }

        $item = $quote->getItemById($itemId);
        $product = $item->getProduct();

        if ($this->helper->isFpxEnabledByProductId($product->getId())) {
            $fpxProductOption = $item->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_PRODUCT_ID);
            $fpxConfigIdOption = $item->getOptionByCode(ConfigurationInfoInterface::FPX_CPQ_COLUMN_ID);
            if (!$fpxProductOption || !$fpxConfigIdOption) {
                return;
            }

            if ($fpxConfigId = $fpxConfigIdOption->getValue()) {
                $this->configurationManagement->delete($fpxConfigId);
            }

            if ($fpxProductId = $fpxProductOption->getValue()) {
                $this->client->deleteObject($fpxProductId);
            }
        }
    }
}
