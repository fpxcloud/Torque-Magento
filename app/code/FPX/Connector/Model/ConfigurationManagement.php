<?php
/**
 * ConfigInfo Helper
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Model;

use FPX\Connector\Helper\ApiRequests;

/**
 * ConfigInfo Helper
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class ConfigurationManagement
{
    /**
     * @var ConfigurationInfoFactory $configInfoFactory
     */
    private $configInfoFactory;

    /**
     * @var ConfigurationInfoRepository $configInfoRepository
     */
    private $configInfoRepository;

    /**
     * @var \FPX\Connector\Model\Api\Client
     */
    private $client;

    public function __construct(
        ConfigurationInfoRepository $configInfoRepository,
        ConfigurationInfoFactory $configInfoFactory,
        \FPX\Connector\Model\Api\Client $client
    ) {
        $this->configInfoRepository = $configInfoRepository;
        $this->configInfoFactory = $configInfoFactory;
        $this->client = $client;
    }

    /**
     * @param array $options
     * @return \FPX\Connector\Model\ConfigurationInfo
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function create(array $options)
    {
        $configData = $this->client->getConfigurationData($options['productId']);

        $configInfo = $this->configInfoFactory->create([
            'data' => [
                ConfigurationInfo::FPX_CPQ_COLUMN_QUOTE_ID => $options['quoteId'],
                ConfigurationInfo::FPX_CPQ_COLUMN_CORRELATION_ID => $options['correlationId'],
                ConfigurationInfo::FPX_CPQ_COLUMN_PRODUCT_ID => $options['productId'],
                ConfigurationInfo::FPX_CPQ_COLUMN_CONFIG_INFO => json_encode($configData)
            ]
        ]);

        return $this->configInfoRepository->save($configInfo);
    }

    /**
     * @param       $id
     * @param array $options
     * @return \FPX\Connector\Model\ConfigurationInfo
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($id, array $options)
    {
        $configData = $this->client->getConfigurationData($options['productId']);

        $configInfo = $this->configInfoRepository->getById($id);
        $configInfo->addData([
            ConfigurationInfo::FPX_CPQ_COLUMN_QUOTE_ID => $options['quoteId'],
            ConfigurationInfo::FPX_CPQ_COLUMN_CORRELATION_ID => $options['correlationId'],
            ConfigurationInfo::FPX_CPQ_COLUMN_PRODUCT_ID => $options['productId'],
            ConfigurationInfo::FPX_CPQ_COLUMN_CONFIG_INFO => json_encode($configData)
        ]);

        return $this->configInfoRepository->save($configInfo);
    }

    /**
     * @param $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete($id)
    {
        return $this->configInfoRepository->deleteById($id);
    }
}
