<?php // phpcs:ignore PSR12.Files.FileHeader.SpacingAfterBlock -- baseline

declare(strict_types=1);

namespace RJDS\LogViewer\Model\LogListing;

use Magento\Framework\Api\Filter;

class LogRowFilter
{
    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, Filter> $filters
     * @return array<int, array<string, mixed>>
     */
    public function apply(array $rows, array $filters): array
    {
        foreach ($filters as $filter) {
            $rows = array_values(array_filter(
                $rows,
                fn (array $row): bool => $this->matchesFilter($row, $filter)
            ));
        }

        return $rows;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function matchesFilter(array $row, Filter $filter): bool
    {
        $field = (string) $filter->getField();
        $condition = (string) ($filter->getConditionType() ?: 'eq');
        $value = $filter->getValue();

        if ($field === 'fulltext') {
            return $this->matchesFulltext($row, (string) $value);
        }

        if (!array_key_exists($field, $row)) {
            return true;
        }

        $currentValue = $row[$field];

        switch ($condition) {
            case 'like':
                return stripos((string) $currentValue, trim((string) $value, '%')) !== false;
            case 'eq':
                return (string) $currentValue === (string) $value;
            case 'gteq':
            case 'from':
                return (float) $currentValue >= (float) $value;
            case 'lteq':
            case 'to':
                return (float) $currentValue <= (float) $value;
            default:
                return true;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function matchesFulltext(array $row, string $needle): bool
    {
        $needle = trim($needle, '% ');
        if ($needle === '') {
            return true;
        }

        return stripos((string) ($row['file_name'] ?? ''), $needle) !== false
            || stripos((string) ($row['file_location'] ?? ''), $needle) !== false;
    }
}

