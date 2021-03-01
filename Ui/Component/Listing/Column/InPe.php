<?php
namespace MW\CB\Ui\Component\Listing\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Sales\Model\OrderFactory;

    class InPe extends Column {

    protected $_orderRepository;
    protected $_orderFactory;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        OrderRepositoryInterface $orderRepository,
        OrderFactory $orderFactory
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])){
            foreach ($dataSource['data']['items'] as & $item){
                $order = $this->_orderFactory->create()->load($item['entity_id']);
                $skus = $order->getAllItems();
                $sku='';
                $i=0;
                foreach ($skus as $itemone) {
                    if ($i==0){
                        $sku = $itemone->getSku();
                    }else{
                        $sku = $sku.','.$itemone->getSku();
                    }
                    $i++;

                }
                $item[$this->getData('name')] = $sku;
            }
        }
        return $dataSource;
    }
}
