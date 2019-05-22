<?php
/**
 * ConfigurationInfo Search Results Interface
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Configuration Info Search Results
 *
 * @category    FPX
 * @package     FPX_Connector
 * @api
 */
interface ConfigurationInfoSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get ConfigurationInfo items list.
     *
     * @return \FPX\Connector\Api\Data\ConfigurationInfoInterface[]
     */
    public function getItems();

    /**
     * Set ConfigurationInfo items list.
     *
     * @param \FPX\Connector\Api\Data\ConfigurationInfoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
