<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use RJDS\LogViewer\Model\Config\Config;

class Actions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        private readonly Config $config,
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
        $deleteEnabled = $this->config->isDeleteEnabled();

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

                if ($deleteEnabled) {
                    $item[$columnName]['delete'] = [
                        'href' => $this->urlBuilder->getUrl('logviewer/log_action/delete', ['id' => $item['id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete log file'),
                            'message' => __('Are you sure you want to delete this log file?'),
                        ],
                        'post' => true,
                    ];
                } else {
                    unset($item[$columnName]['delete']);
                }
            }
        }

        return $dataSource;
    }
}

