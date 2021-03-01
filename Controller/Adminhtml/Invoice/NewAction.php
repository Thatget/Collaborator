<?php

namespace MW\CB\Controller\Adminhtml\Invoice;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
