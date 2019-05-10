<?php
/**
 * ConfigurationInfo Interface
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Api\Data;

/**
 * ConfigurationInfo Interface
 *
 * @category    FPX
 * @package     FPX_Connector
 * @api
 */
interface ConfigurationInfoInterface
{
    /**#@+
     * Constants used as keys in fpx_cpq_configurations table.
     *
     * @var string
     */
    const FPX_CPQ_TABLE                     = 'fpx_cpq_configurations';
    const FPX_CPQ_COLUMN_ID                 = 'config_id';
    const FPX_CPQ_COLUMN_QUOTE_ID           = 'cpq_quote_id';
    const FPX_CPQ_COLUMN_CORRELATION_ID     = 'cpq_correlation_id';
    const FPX_CPQ_COLUMN_PRODUCT_ID         = 'cpq_product_id';
    const FPX_CPQ_COLUMN_CONFIG_INFO        = 'config_info';
    /**#@-*/

    /**
     * Get config ID
     *
     * @return int
     */
    public function getConfigId();

    /**
     * Set config ID
     *
     * @param int $configId
     * @return $this
     */
    public function setConfigId($configId);

    /**
     * Get CPQ Quote ID
     *
     * @return string
     */
    public function getCpqQuoteId();

    /**
     * Set CPQ Quote ID
     *
     * @param string $cpqQuoteId
     * @return $this
     */
    public function setCpqQuoteId($cpqQuoteId);

    /**
     * Get CPQ Correlation ID
     *
     * @return string
     */
    public function getCpqCorrelationId();

    /**
     * Set CPQ Correlation ID
     *
     * @param string $cpqCorrelationId
     * @return $this
     */
    public function setCpqCorrelationId($cpqCorrelationId);

    /**
     * Get CPQ Correlation ID
     *
     * @return string
     */
    public function getCpqProductId();

    /**
     * Set CPQ Correlation ID
     *
     * @param string $cpqProductId
     * @return $this
     */
    public function setCpqProductId($cpqProductId);

    /**
     * Get CPQ config info
     *
     * @return string
     */
    public function getConfigInfo();

    /**
     * Set CPQ config info
     *
     * @param string $configInfo
     * @return $this
     */
    public function setConfigInfo($configInfo);

    /**
     * Get Price from config info
     *
     * @return int|null
     */
    public function getPrice();
}
