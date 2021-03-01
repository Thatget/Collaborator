<?php
namespace MW\CB\Model;
class Mwip extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'mw_cachetag_mwip';

    protected $_cacheTag = 'mw_cachetag_mwip';

    protected $_eventPrefix = 'mw_eventprifixtag_mwip';

    protected function _construct()
    {
        $this->_init('MW\CB\Model\ResourceModel\Mwip');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
