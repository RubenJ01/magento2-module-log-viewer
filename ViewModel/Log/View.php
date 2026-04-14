<?php

declare(strict_types=1);

namespace RJDS\LogViewer\ViewModel\Log;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;

class View implements ArgumentInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private readonly LogFileRowsLoader $rowsLoader,
    ) {
    }

    /**
     * @return string[]
     * @throws FileSystemException
     */
    public function getLogEntries(): array
    {
        $filePath = $this->resolveFilePath();

        if ($filePath === null) {
            return [];
        }

        $limit = max(1, (int) ($this->request->getParam('limit') ?: 100));
        $lines = array_reverse(file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        return array_slice($lines, 0, $limit);
    }

    public function getLimit(): int
    {
        return max(1, (int) ($this->request->getParam('limit') ?: 100));
    }

    /**
     * @throws FileSystemException
     */
    public function getFileName(): string
    {
        $row = $this->getRowById($this->getId());

        return (string) ($row['file_name'] ?? '');
    }

    public function getId(): int
    {
        return (int) $this->request->getParam('id');
    }

    /**
     * @throws FileSystemException
     */
    private function resolveFilePath(): ?string
    {
        $row = $this->getRowById($this->getId());

        if ($row === null || !isset($row['file_location'])) {
            return null;
        }

        $absolutePath = BP . '/' . ltrim((string) $row['file_location'], '/');

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }

        return $absolutePath;
    }

    /**
     * @return array<string, mixed>|null
     * @throws FileSystemException
     */
    private function getRowById(int $id): ?array
    {
        foreach ($this->rowsLoader->load() as $row) {
            if ((int) ($row['id'] ?? 0) === $id) {
                return $row;
            }
        }

        return null;
    }
}
