<?php
namespace MW\CB\Model\ResourceModel;


class Mwip extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('mw_invoice_payment', 'ip_id');
    }

}
