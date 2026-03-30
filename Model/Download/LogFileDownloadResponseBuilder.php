<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Download;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;

class LogFileDownloadResponseBuilder
{
    public function __construct(
        private readonly FileFactory $fileFactory,
        private readonly DirectoryList $directoryList
    ) {
    }

    /**
     * @throws LocalizedException
     * @throws Exception
     */
    public function build(string $downloadName, string $absolutePath): ResponseInterface
    {
        $relativePath = $this->toRootRelativePath($absolutePath);

        return $this->fileFactory->create(
            basename($downloadName),
            [
                'type' => 'filename',
                'value' => $relativePath,
                'rm' => false,
            ]
        );
    }

    /**
     * @throws LocalizedException
     */
    private function toRootRelativePath(string $absolutePath): string
    {
        $rootDirectory = rtrim($this->directoryList->getRoot(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!str_starts_with($absolutePath, $rootDirectory)) {
            throw new LocalizedException(__('Invalid file path for download response.'));
        }

        return ltrim(substr($absolutePath, strlen($rootDirectory)), DIRECTORY_SEPARATOR);
    }
}

