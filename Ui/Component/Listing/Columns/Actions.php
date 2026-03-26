<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

class Actions extends Column
{
    /**
     * @param array<string, mixed> $dataSource
     * @return array<string, mixed>
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items']) || !is_array($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item[$this->getData('name')]) || !is_array($item[$this->getData('name')])) {
                $item[$this->getData('name')] = [];
            }
        }

        return $dataSource;
    }
}

