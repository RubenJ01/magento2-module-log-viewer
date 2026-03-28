<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Selection;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;
use RJDS\LogViewer\Model\Path\LogFilePathResolver;

class SelectedLogRowsResolver
{
    public function __construct(
        private readonly LogFileRowsLoader $rowsLoader,
        private readonly LogFilePathResolver $filePathResolver
    ) {
    }

    /**
     * @return array<int>
     * @throws FileSystemException
     */
    public function resolveRequestedIds(RequestInterface $request): array
    {
        $selected = $request->getParam('selected');
        $excluded = $request->getParam('excluded');

        if (is_array($selected)) {
            return array_values(array_unique(array_map('intval', $selected)));
        }

        if ($excluded === 'false') {
            return array_map('intval', array_column($this->rowsLoader->load(), 'id'));
        }

        return [];
    }

    /**
     * @param array<int> $requestedIds
     * @return array<int, array<string, mixed>>
     * @throws FileSystemException
     */
    public function resolveRowsByIds(array $requestedIds): array
    {
        $requestedIndex = array_fill_keys($requestedIds, true);
        $rows = [];

        foreach ($this->rowsLoader->load() as $row) {
            $id = (int) ($row['id'] ?? 0);
            if (!isset($requestedIndex[$id])) {
                continue;
            }

            $absolutePath = $this->filePathResolver->resolveReadablePath((string) ($row['file_location'] ?? ''));
            if ($absolutePath === null) {
                continue;
            }

            $row['absolute_path'] = $absolutePath;
            $rows[] = $row;
        }

        return $rows;
    }
}


