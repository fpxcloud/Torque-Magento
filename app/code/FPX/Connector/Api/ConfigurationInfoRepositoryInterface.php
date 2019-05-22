<?php
/**
 * ConfigurationInfo Repository Interface
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Api;

/**
 * ConfigurationInfo Repository Interface
 * Provides CRUD methods
 *
 * @category    FPX
 * @package     FPX_Connector
 * @api
 */
interface ConfigurationInfoRepositoryInterface
{
    /**
     * Save Configuration Info.
     *
     * @param \FPX\Connector\Api\Data\ConfigurationInfoInterface $config
     * @return \FPX\Connector\Api\Data\ConfigurationInfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ConfigurationInfoInterface $config);

    /**
     * Retrieve Configuration Info.
     *
     * @param int $id
     * @return \FPX\Connector\Api\Data\ConfigurationInfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Configuration Info list matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \FPX\Connector\Api\Data\ConfigurationInfoSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Configuration Info.
     *
     * @param \FPX\Connector\Api\Data\ConfigurationInfoInterface $config
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ConfigurationInfoInterface $config);

    /**
     * Delete Configuration Info by id.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
