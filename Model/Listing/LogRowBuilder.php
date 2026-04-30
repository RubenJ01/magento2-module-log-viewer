<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\Listing;

use DateTimeImmutable;
use Rjds\PhpHumanize\HumanizerInterface;
use SplFileInfo;

class LogRowBuilder
{
    private const LOG_PATH_PREFIX = 'var/log/';

    public function __construct(
        private readonly HumanizerInterface $humanizer
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function build(int $id, SplFileInfo $fileInfo, string $logDirectory): array
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
        ];
    }

    private function buildRelativePath(string $absolutePath, string $logDirectory): string
    {
        $normalizedAbsolutePath = str_replace('\\', '/', $absolutePath);
        $normalizedLogDirectory = rtrim(str_replace('\\', '/', $logDirectory), '/') . '/';
        if (!str_starts_with($normalizedAbsolutePath, $normalizedLogDirectory)) {
            return self::LOG_PATH_PREFIX . basename($absolutePath);
        }

        $relativePath = ltrim(substr($normalizedAbsolutePath, strlen($normalizedLogDirectory)), '/');

        return self::LOG_PATH_PREFIX . $relativePath;
    }
}

