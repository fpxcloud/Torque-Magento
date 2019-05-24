<?php
/**
 * ConfigurationInfo Collection
 *
 * @category     FPX
 * @package      FPX_Connector
 * @copyright    Copyright (c) 2017 FPX (www.fpx.com)
 */

namespace FPX\Connector\Model\ResourceModel\ConfigurationInfo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use FPX\Connector\Model\ConfigurationInfo;
use FPX\Connector\Model\ResourceModel\ConfigurationInfo as ResourceModel;

/**
 * ConfigurationInfo Collection
 *
 * @category    FPX
 * @package     FPX_Connector
 */
class Collection extends AbstractCollection
{
    /**
     * collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ConfigurationInfo::class, ResourceModel::class);
    }
}
