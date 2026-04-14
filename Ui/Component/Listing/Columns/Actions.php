<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array<string, mixed> $dataSource
     * @return array<string, mixed>
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items']) || !is_array($dataSource['data']['items'])) {
            return $dataSource;
        }

        $columnName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item[$columnName]) || !is_array($item[$columnName])) {
                $item[$columnName] = [];
            }

            if (isset($item['id'])) {
                $item[$columnName]['view'] = [
                    'href' => $this->urlBuilder->getUrl('logviewer/log_action/view', ['id' => $item['id']]),
                    'label' => __('View'),
                ];

                $item[$columnName]['download'] = [
                    'href' => $this->urlBuilder->getUrl('logviewer/log_action/download', ['id' => $item['id']]),
                    'label' => __('Download'),
                ];
            }
        }

        return $dataSource;
    }
}

