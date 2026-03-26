<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\LogListing;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class LogFileRowsLoader
{
    /** @var array<int, array<string, mixed>>|null */
    private ?array $rows = null;

    public function __construct(
        private readonly DirectoryList $directoryList,
        private readonly LogRowFactory $rowFactory
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

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($logDirectory, \FilesystemIterator::SKIP_DOTS)
        );

        $id = 1;
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $this->rows[] = $this->rowFactory->create($id++, $fileInfo, $logDirectory);
        }

        return $this->rows;
    }
}

