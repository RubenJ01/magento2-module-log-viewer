<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\LogListing;

class LogRowSorter
{
    /** @var array<string, string> */
    private const SORT_FIELD_MAP = [
        'file_size' => 'file_size_bytes',
        'last_modified' => 'last_modified_timestamp',
    ];

    /**
     * @param array<int, array<string, mixed>> $rows
     * @param array<int, array{field:string,direction:string}> $orders
     * @return array<int, array<string, mixed>>
     */
    public function sort(array $rows, array $orders): array
    {
        if ($orders === []) {
            return $rows;
        }

        usort($rows, function (array $left, array $right) use ($orders): int {
            foreach ($orders as $order) {
                $field = $order['field'];
                $direction = $order['direction'];
                $result = $this->compareByField($left, $right, $field);

                if ($result !== 0) {
                    return $direction === 'DESC' ? -$result : $result;
                }
            }

            return 0;
        });

        return $rows;
    }

    /**
     * @param array<string, mixed> $left
     * @param array<string, mixed> $right
     */
    private function compareByField(array $left, array $right, string $field): int
    {
        $field = self::SORT_FIELD_MAP[$field] ?? $field;
        $leftValue = $left[$field] ?? '';
        $rightValue = $right[$field] ?? '';

        if (in_array($field, ['id', 'file_size_bytes', 'last_modified_timestamp'], true)) {
            return (int) $leftValue <=> (int) $rightValue;
        }

        return strnatcasecmp((string) $leftValue, (string) $rightValue);
    }
}

