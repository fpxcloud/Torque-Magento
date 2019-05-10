<?php
/**
 * ConfigurationInfo Repository
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use FPX\Connector\Api\Data;
use FPX\Connector\Api\ConfigurationInfoRepositoryInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * ConfigurationInfo Repository
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class ConfigurationInfoRepository implements ConfigurationInfoRepositoryInterface
{
    /**
     * @var array
     */
    private $registry = [];

    /**
     * @var ResourceModel\ConfigurationInfo
     */
    private $resource;

    /**
     * @var ConfigurationInfoFactory
     */
    private $configInfoFactory;

    /**
     * @var Data\ConfigurationInfoSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var ResourceModel\ConfigurationInfo\CollectionFactory
     */
    private $configInfoCollectionFactory;

    /**
     * @param ResourceModel\ConfigurationInfo $resource
     * @param ConfigurationInfoFactory $configInfoFactory
     * @param Data\ConfigurationInfoSearchResultsInterfaceFactory $searchResultsFactory
     * @param ResourceModel\ConfigurationInfo\CollectionFactory $configInfoCollectionFactory
     */
    public function __construct(
        ResourceModel\ConfigurationInfo $resource,
        ConfigurationInfoFactory $configInfoFactory,
        Data\ConfigurationInfoSearchResultsInterfaceFactory $searchResultsFactory,
        ResourceModel\ConfigurationInfo\CollectionFactory $configInfoCollectionFactory
    ) {
        $this->resource = $resource;
        $this->configInfoFactory = $configInfoFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->configInfoCollectionFactory = $configInfoCollectionFactory;
    }

    /**
     * Save Configuration Info.
     *
     * @param Data\ConfigurationInfoInterface $configInfo
     * @return ConfigurationInfo
     * @throws CouldNotSaveException
     */
    public function save(Data\ConfigurationInfoInterface $configInfo)
    {
        try {
            $configInfo->getResource()->save($configInfo);
            $this->registry[$configInfo->getId()] = $configInfo;
        } catch (\Exception $e) {
            throw new CouldNotSaveException($e->getMessage());
        }

        return $this->registry[$configInfo->getId()];
    }

    /**
     * Retrieve Configuration Info.
     *
     * @param int $id
     * @return ConfigurationInfo
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->registry[$id])) {
            $configInfo = $this->configInfoFactory->create();
            $this->resource->load($configInfo, $id);
            if (!$configInfo->getId()) {
                throw new NoSuchEntityException(
                    __('Configuration Info with ID "%1" does not exist.', $id)
                );
            }
            $this->registry[$id] = $configInfo;
        }
        return $this->registry[$id];
    }

    /**
     * Retrieve Configuration Info list matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \FPX\Connector\Api\Data\ConfigurationInfoSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \FPX\Connector\Api\Data\ConfigurationInfoSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \FPX\Connector\Model\ResourceModel\ConfigurationInfo\Collection $collection */
        $collection = $this->configInfoCollectionFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                if ($sortOrder->getField()) {
                    $collection->addOrder(
                        $sortOrder->getField(),
                        ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? SortOrder::SORT_ASC : SortOrder::SORT_DESC
                    );
                }
            }
        }
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    /**
     * Delete Configuration Info.
     *
     * @param Data\ConfigurationInfoInterface $configInfo
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\ConfigurationInfoInterface $configInfo)
    {
        try {
            $configInfo->getResource()->delete($configInfo);
            unset($this->registry[$configInfo->getId()]);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete Configuration Info by id.
     *
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
