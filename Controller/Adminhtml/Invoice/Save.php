<?php

namespace MW\CB\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use MW\CB\Model\MwipFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;

/**
 * Class for saving of customer address
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface{

    protected $mwipFactory;

    protected $orderFactory;

    protected $orderRepository;

    protected $invoiceService;

    protected $transaction;


    public function __construct(
        Context $context,
        MwipFactory $mwipFactory,
        OrderFactory $orderFactory,
        OrderRepositoryInterface $orderRepository,
        InvoiceService $invoiceService,
        Transaction $transaction
    )
    {
        $this->mwipFactory = $mwipFactory;
        $this->orderFactory = $orderFactory;
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $amount = (float)$this->getRequest()->getParam('amount');
        $orderIncrementId = $this->getRequest()->getParam('ip_order_id');
        $comment = $this->getRequest()->getParam('comment');

        $order = $this->orderFactory->create()->loadByIncrementId((int)$orderIncrementId);
        if ($order->getId()){

            $mwipCollection = $this->mwipFactory->create();
            $all = $mwipCollection->getCollection()->addFieldToFilter('ip_order_id', ['eq' => $orderIncrementId])->getData();
            $totalAmount = 0;
            if (!empty($all)){
                foreach ($all as $item) {
                    $totalAmount =$totalAmount + $item['amount'];
                }
            }
            $orderGrandTotal = $this->orderFactory->create()->loadByIncrementId((int)$orderIncrementId)->getGrandTotal();
            if ($totalAmount >= $orderGrandTotal){
                $this->messageManager->addErrorMessage(__('Invoice already exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            //Save Data
            else{
                $data['amount'] = $amount;
                $data['ip_order_id'] = $orderIncrementId;
                $data['comment'] = $comment;
                $mwipCollection->setData($data);
                $mwipCollection->save();
            }
            //Create Invoice for this Order
            $totalAmountAll = $amount+$totalAmount;
            if ($totalAmountAll >= $orderGrandTotal){
                $this->createInvoice($order);
                $this->messageManager->addSuccessMessage(__('Invoice has been created'));
            }

            return $resultRedirect->setPath('*/*/');
        }else{
            $this->messageManager->addErrorMessage(__('This order does not exist.'));
            return $resultRedirect->setPath('*/*/new');
        }
    }

    /**
     * Create Invoice
     *
     * @param   Magento\Sales\Model\OrderFactory $order
     */
    public function createInvoice($order){
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->getOrder()->setIsInProcess(true);
            $invoice->register();
            $invoice->save();
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
            //$this->invoiceSender->send($invoice);
            //Send Invoice mail to customer
            $order->addStatusHistoryComment(
                __('Notified customer about invoice creation #%1.', $invoice->getId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }
}
