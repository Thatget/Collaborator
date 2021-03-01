<?php

namespace MW\CB\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column {

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['customer_group_id'])) {
                    $title = $this->escaper->escapeHtml($item['customer_group_code']);
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'id' => $item['customer_group_id']
                                ]
                            ),
                            'label' => __('Edit'),
                        ],
                    ];

                    if (!$this->groupManagement->isReadonly($item['customer_group_id'])) {
                        $item[$this->getData('name')]['delete'] = [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'id' => $item['customer_group_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete %1', $this->escaper->escapeHtml($title)),
                                'message' => __(
                                    'Are you sure you want to delete a %1 record?',
                                    $this->escaper->escapeHtml($title)
                                )
                            ],
                            'post' => true,
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
