<?php

declare(strict_types=1);

namespace RJDS\LogViewer\ViewModel\Log;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use RJDS\LogViewer\Model\Listing\LogFileRowsLoader;

class View implements ArgumentInterface
{
    private const DEFAULT_LIMIT = 100;
    private const LIMIT_OPTIONS = [50, 100, 250, 500, 1000];
    private const LEVEL_MAP = [
        '.CRITICAL' => 'critical',
        '.ERROR'    => 'error',
        '.WARNING'  => 'warning',
        '.INFO'     => 'info',
        '.DEBUG'    => 'debug',
    ];

    private const LEVEL_OPTIONS = [
        'critical' => 'Critical',
        'error'    => 'Error',
        'warning'  => 'Warning',
        'info'     => 'Info',
        'debug'    => 'Debug',
    ];

    private ?array $lines = null;
    private ?array $filtered = null;

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
        $limit  = $this->getLimit();
        $offset = ($this->getPage() - 1) * $limit;

        return array_slice($this->getFilteredEntries(), $offset, $limit);
    }

    /**
     * @throws FileSystemException
     */
    public function getTotalCount(): int
    {
        return count($this->getFilteredEntries());
    }

    /**
     * @throws FileSystemException
     */
    public function getTotalPages(): int
    {
        return max(1, (int) ceil($this->getTotalCount() / $this->getLimit()));
    }

    public function getLevel(string $line): string
    {
        foreach (self::LEVEL_MAP as $needle => $level) {
            if (str_contains($line, $needle)) {
                return $level;
            }
        }

        return 'default';
    }

    public function getLevelOptions(): array
    {
        return self::LEVEL_OPTIONS;
    }

    public function highlight(string $escapedLine, string $q): string
    {
        if ($q === '') {
            return $escapedLine;
        }

        return preg_replace(
            '/' . preg_quote($q, '/') . '/i',
            '<mark>$0</mark>',
            $escapedLine
        ) ?? $escapedLine;
    }

    public function getLimit(): int
    {
        return max(1, (int) ($this->request->getParam('limit') ?: self::DEFAULT_LIMIT));
    }

    public function getPage(): int
    {
        return max(1, (int) ($this->request->getParam('p') ?: 1));
    }

    public function getSearchQuery(): string
    {
        return (string) ($this->request->getParam('q') ?? '');
    }

    public function getLevelFilter(): string
    {
        return (string) ($this->request->getParam('level') ?? '');
    }

    /**
     * @return int[]
     */
    public function getLimitOptions(): array
    {
        return self::LIMIT_OPTIONS;
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
     * @return string[]
     * @throws FileSystemException
     */
    private function getFilteredEntries(): array
    {
        if ($this->filtered !== null) {
            return $this->filtered;
        }

        $entries = $this->getLines();
        $level = $this->getLevelFilter();
        $q = $this->getSearchQuery();

        if ($level !== '') {
            $entries = array_filter(
                $entries,
                fn (string $line): bool => $this->getLevel($line) === $level
            );
        }

        if ($q !== '') {
            $entries = array_filter(
                $entries,
                static fn (string $line): bool => stripos($line, $q) !== false
            );
        }

        return $this->filtered = array_values($entries);
    }

    /**
     * @return string[]
     * @throws FileSystemException
     */
    private function getLines(): array
    {
        if ($this->lines !== null) {
            return $this->lines;
        }

        $filePath = $this->resolveFilePath();

        if ($filePath === null) {
            return $this->lines = [];
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return $this->lines = array_reverse($lines ?: []);
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
