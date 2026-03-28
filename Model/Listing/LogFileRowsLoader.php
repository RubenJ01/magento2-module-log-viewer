<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Listing;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LogFileRowsLoader
{
    /** @var array<int, array<string, mixed>>|null */
    private ?array $rows = null;

    public function __construct(
        private readonly DirectoryList $directoryList,
        private readonly LogRowBuilder $rowBuilder
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     * @throws FileSystemException
     */
    public function load(): array
    {
        if ($this->rows !== null) {
            return $this->rows;
        }

        $this->rows = [];
        $logDirectory = $this->directoryList->getPath(DirectoryList::LOG);

        if (!is_dir($logDirectory)) {
            return $this->rows;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($logDirectory, \FilesystemIterator::SKIP_DOTS)
        );

        $id = 1;
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $this->rows[] = $this->rowBuilder->build($id++, $fileInfo, $logDirectory);
        }

        return $this->rows;
    }
}


