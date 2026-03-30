<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Path;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class LogFilePathResolver
{
    public function __construct(
        private readonly DirectoryList $directoryList
    ) {
    }

    /**
     * @throws FileSystemException
     */
    public function resolveReadablePath(string $relativePath): ?string
    {
        $rootDirectory = $this->directoryList->getRoot();
        $logDirectory = $this->directoryList->getPath(DirectoryList::LOG);
        $normalizedLogDirectory = realpath($logDirectory);
        if ($normalizedLogDirectory === false) {
            return null;
        }

        $absolutePath = realpath($rootDirectory . DIRECTORY_SEPARATOR . ltrim($relativePath, DIRECTORY_SEPARATOR));
        $normalizedLogDirectory = rtrim($normalizedLogDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if ($absolutePath === false || !str_starts_with($absolutePath, $normalizedLogDirectory)) {
            return null;
        }

        if (!is_file($absolutePath) || !is_readable($absolutePath)) {
            return null;
        }

        return $absolutePath;
    }
}


