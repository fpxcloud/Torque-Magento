<?php
/**
 * ConfigurationInfo Model
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use FPX\Connector\Api\Data\ConfigurationInfoInterface;
use FPX\Connector\Model\ResourceModel\ConfigurationInfo as ResourceModel;

/**
 * ConfigurationInfo Model
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class ConfigurationInfo extends AbstractModel implements ConfigurationInfoInterface, IdentityInterface
{
    /**
     * Configuration Info cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'fpx_cpq_config';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get config ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->getConfigId();
    }

    /**
     * Set config ID
     *
     * @param int $configId
     * @return $this
     */
    public function setId($configId)
    {
        return $this->setConfigId($configId);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigId()
    {
        return $this->getData(static::FPX_CPQ_COLUMN_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigId($configId)
    {
        $this->setData(static::FPX_CPQ_COLUMN_ID, $configId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCpqQuoteId()
    {
        return $this->getData(static::FPX_CPQ_COLUMN_QUOTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCpqQuoteId($cpqQuoteId)
    {
        $this->setData(static::FPX_CPQ_COLUMN_QUOTE_ID, $cpqQuoteId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCpqCorrelationId()
    {
        return $this->getData(static::FPX_CPQ_COLUMN_CORRELATION_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCpqCorrelationId($cpqCorrelationId)
    {
        $this->setData(static::FPX_CPQ_COLUMN_CORRELATION_ID, $cpqCorrelationId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCpqProductId()
    {
        return $this->getData(static::FPX_CPQ_COLUMN_PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCpqProductId($cpqProductId)
    {
        $this->setData(static::FPX_CPQ_COLUMN_PRODUCT_ID, $cpqProductId);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigInfo()
    {
        return $this->getData(static::FPX_CPQ_COLUMN_CONFIG_INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigInfo($configInfo)
    {
        $this->setData(static::FPX_CPQ_COLUMN_CONFIG_INFO, $configInfo);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        $data = json_decode($this->getConfigInfo(), true);

        if (!isset($data['products'][0]['extendedTotalList'])) {
            return null;
        }

        return $data['products'][0]['extendedTotalList'];
    }
}
