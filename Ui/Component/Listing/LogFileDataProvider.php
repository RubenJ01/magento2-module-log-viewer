<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Ui\Component\Listing;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;
use RJDS\LogViewer\Model\Listing\LogRowSorter;

class LogFileDataProvider extends AbstractDataProvider
{
    /** @var array<int, array{field:string,direction:string}> */
    private array $orders = [];
    private int $currentPage = 1;
    private int $pageSize = 20;
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        private readonly LogFileRowsLoader $rowsLoader,
        private readonly LogRowSorter $rowSorter,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    public function addFilter(Filter $filter): void
    {
        // TODO
    }
    public function addOrder($field, $direction): void
    {
        $this->orders[] = [
            'field' => (string) $field,
            'direction' => strtoupper((string) $direction) === 'DESC' ? 'DESC' : 'ASC',
        ];
    }
    public function setLimit($offset, $size): void
    {
        $this->currentPage = max(1, (int) $offset);
        $this->pageSize = max(1, (int) $size);
    }
    public function getData(): array
    {
        $items = $this->getPreparedRows();
        $totalRecords = count($items);
        $start = ($this->currentPage - 1) * $this->pageSize;
        $items = array_slice($items, $start, $this->pageSize);
        return [
            'totalRecords' => $totalRecords,
            'items' => array_values($items),
        ];
    }
    public function getAllIds(): array
    {
        return array_column($this->getPreparedRows(), 'id');
    }
    public function count(): ?int
    {
        return count($this->getPreparedRows());
    }
    /**
     * @return array<int, array<string, mixed>>
     */
    private function getPreparedRows(): array
    {
        return $this->rowSorter->sort($this->rowsLoader->load(), $this->orders);
    }
}
