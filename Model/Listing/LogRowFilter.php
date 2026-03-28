<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Listing;

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
        $field = $filter->getField();
        $condition = $filter->getConditionType() ?: 'eq';
        $value = $filter->getValue();

        if ($field === 'fulltext') {
            return $this->matchesFulltext($row, (string) $value);
        }

        if (!array_key_exists($field, $row)) {
            return true;
        }

        $currentValue = $row[$field];

        return match ($condition) {
            'like' => stripos((string) $currentValue, trim($value, '%')) !== false,
            'eq' => (string) $currentValue === $value,
            'gteq', 'from' => (float) $currentValue >= (float) $value,
            'lteq', 'to' => (float) $currentValue <= (float) $value,
            default => true,
        };
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


