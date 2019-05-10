<?php
/**
 * ConfigurationInfo ResourceModel
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Model\ResourceModel;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use FPX\Connector\Api\Data\ConfigurationInfoInterface;

/**
 * ConfigurationInfo ResourceModel
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class ConfigurationInfo extends AbstractDb
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $connectionName = null
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            ConfigurationInfoInterface::FPX_CPQ_TABLE,
            ConfigurationInfoInterface::FPX_CPQ_COLUMN_ID
        );
    }

    /**
     * Load an object
     *
     * @param \FPX\Connector\Model\ConfigurationInfo|AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $field; // for suppress Generic.CodeAnalysis.UnusedFunctionParameter.Found warning

        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }
}
