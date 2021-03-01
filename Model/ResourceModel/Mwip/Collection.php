<?php
namespace MW\CB\Model\ResourceModel\Mwip;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'ip_id';
    protected $_eventPrefix = 'mw_cb_mwip_collection';
    protected $_eventObject = 'mwip_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('MW\CB\Model\Mwip', 'MW\CB\Model\ResourceModel\Mwip');
    }

}
