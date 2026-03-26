<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\LogListing;

use DateTimeImmutable;
use Rjds\PhpHumanize\HumanizerInterface;
use SplFileInfo;

class LogRowFactory
{
    private const LOG_PATH_PREFIX = 'var/log/';

    public function __construct(
        private readonly HumanizerInterface $humanizer
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function create(int $id, SplFileInfo $fileInfo, string $logDirectory): array
    {
        $fileSizeBytes = max(0, (int) $fileInfo->getSize());
        $lastModifiedTimestamp = max(0, (int) $fileInfo->getMTime());
        $lastModifiedDate = (new DateTimeImmutable())->setTimestamp($lastModifiedTimestamp);

        return [
            'id' => $id,
            'file_name' => $fileInfo->getFilename(),
            'file_location' => $this->buildRelativePath($fileInfo->getPathname(), $logDirectory),
            'file_size' => $this->humanizer->fileSize($fileSizeBytes),
            'file_size_bytes' => $fileSizeBytes,
            'last_modified' => $this->humanizer->diffForHumans($lastModifiedDate),
            'last_modified_timestamp' => $lastModifiedTimestamp,
            'actions' => $this->buildActions(),
        ];
    }

    private function buildRelativePath(string $absolutePath, string $logDirectory): string
    {
        $relativePath = ltrim(substr($absolutePath, strlen($logDirectory)), DIRECTORY_SEPARATOR);

        return self::LOG_PATH_PREFIX . $relativePath;
    }

    /**
     * @return array<string, array{href:string,label:\Magento\Framework\Phrase}>
     */
    private function buildActions(): array
    {
        return [
            'view' => [
                'href' => 'javascript:void(0)',
                'label' => __('View'),
            ],
            'download' => [
                'href' => 'javascript:void(0)',
                'label' => __('Download'),
            ],
            'delete' => [
                'href' => 'javascript:void(0)',
                'label' => __('Delete'),
            ],
        ];
    }
}

