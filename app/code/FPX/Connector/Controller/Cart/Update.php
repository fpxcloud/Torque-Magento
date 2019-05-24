<?php
/**
 * FPX add to cart controller
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Controller\Cart;

use FPX\Connector\Helper\Config;
use FPX\Connector\Model\ConfigurationManagement;
use FPX\Connector\Model\ConfigurationInfo;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Form\FormKey;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Checkout\Model\Cart;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * FPX update cart controller
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Update extends Action
{
    const BUY_REQUEST_OPTION_CODE = 'info_buyRequest';

    /**
     * Cached resources singleton
     *
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var FormKey $formKey
     */
    private $formKey;

    /**
     * @var Cart $cart
     */
    private $cart;

    /**
     * @var ConfigurationManagement $configManagement
     */
    private $configManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Update constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \FPX\Connector\Model\ConfigurationManagement $configManagement
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        ResourceConnection $resource,
        FormKey $formKey,
        Cart $cart,
        ConfigurationManagement $configManagement,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resource = $resource;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->configManagement = $configManagement;
        $this->logger = $logger;
    }

    /**
     * Update existing configuration of a cart item
     *
     * @return void
     */
    public function execute()
    {
        $configOptions = $this->getCpqResponseData();

        $item = $this->getItemByConfigId($this->getCpqOldConfigId());
        if (!$item) {
            return;
        }

        $itemConfigId = $item->getOptionByCode(ConfigurationInfo::FPX_CPQ_COLUMN_ID);
        $itemConfigId = $itemConfigId ? $itemConfigId->getValue() : null;

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->resource->getConnection();
        $connection->beginTransaction();
        try {
            if (!$itemConfigId) {
                throw new CouldNotSaveException(__('Old configuration ID is not set'));
            }

            $newConfigInfo = $this->configManagement->update($itemConfigId, $configOptions);
            $this->updateItemOptions($item, $configOptions, $newConfigInfo);
            $connection->commit();
        } catch (LocalizedException $e) {
            $connection->rollBack();
            $this->messageManager->addErrorMessage(__('Something went wrong'));
            $this->logger->critical($e);
        }
    }

    /**
     * Find item having a given configuration
     *
     * @param int $configId
     * @return ItemInterface|null
     */
    private function getItemByConfigId($configId)
    {
        $result = null;
        $items = $this->cart->getItems();

        /** @var ItemInterface $item */
        foreach ($items as $item) {
            $configIdOption = $item->getOptionByCode(ConfigurationInfo::FPX_CPQ_COLUMN_PRODUCT_ID);
            if ($configIdOption && $configIdOption->getValue() == $configId) {
                $result = $item;
                break;
            }
        }

        return $result;
    }

    /**
     *
     * @param ItemInterface $item
     * @param array $configOptions
     * @param ConfigurationInfo $configInfo
     * @throws LocalizedException
     */
    private function updateItemOptions(ItemInterface $item, array $configOptions, ConfigurationInfo $configInfo)
    {
        $newConfigInfo = json_decode($configInfo->getConfigInfo(), true);
        $customPrice = isset($newConfigInfo['products'][0]['extendedTotalList'])
            ? $newConfigInfo['products'][0]['extendedTotalList']
            : 0;

        if (!$customPrice) {
            throw new CouldNotSaveException(__('Price not set'));
        }

        $buyRequest = $item->getBuyRequest()->getData();

        /**
         * prepare additional info to put it to buyRequest
         */
        $additionalItemOptions = [];
        foreach ($item->getOptions() as $option) {
            $optionCode = $option->getCode();
            if ($optionCode == self::BUY_REQUEST_OPTION_CODE) {
                continue;
            }
            $additionalItemOptions[$optionCode] = $option->getValue();
        }

        // replace with new info
        $additionalItemOptions[ConfigurationInfo::FPX_CPQ_COLUMN_PRODUCT_ID] = $configOptions['productId'];
        $additionalItemOptions[ConfigurationInfo::FPX_CPQ_COLUMN_ID] = $configInfo->getId();

        /**
         * update buyRequest to use the data later in plugin inside the Quote method addProduct()
         */
        foreach ($additionalItemOptions as $code => $option) {
            // this will be used to add options to product
            $buyRequest['fpx_options'][$code] = $option;
        }
        // save custom price
        $buyRequest['fpx_options']['custom_price'] = $customPrice;

        $buyRequest['id'] = $item->getId();
        $buyRequest['product'] = $item->getProduct()->getId();
        $buyRequest['form_key'] = $this->formKey->getFormKey();
        // replace with new info
        $buyRequest[Config::PRODUCT_DATA_PREFIX] = $configOptions;

        $item->getBuyRequest()->setData($buyRequest);

        /**
         * Update cart item
         */
        $this->cart->updateItem($item->getId(), $buyRequest);
        // this internally calls Quote method addProduct()
        $this->cart->save();
    }

    /**
     * Get CPQ productId by request
     *
     * @return mixed
     */
    private function getCpqResponseData()
    {
        return $this->getRequest()->getParam(Config::PRODUCT_DATA_PREFIX);
    }

    /**
     * Get Old CPQ Config Id
     *
     * @return mixed
     */
    private function getCpqOldConfigId()
    {
        return $this->getRequest()->getParam(Config::PRODUCT_DATA_PREFIX . 'old_config');
    }
}
