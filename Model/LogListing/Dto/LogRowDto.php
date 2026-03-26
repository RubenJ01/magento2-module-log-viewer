<?php

declare(strict_types=1);

namespace RJDS\LogViewer\Model\LogListing\Dto;

use Rjds\PhpDto\Attribute\CastTo;
use Rjds\PhpDto\Attribute\MapFrom;

class LogRowDto
{
    /**
     * @param array<string, array{href:string,label:string}> $actions
     */
    public function __construct(
        #[CastTo('int')]
        private readonly int $id,
        #[MapFrom('file_name')]
        private readonly string $fileName,
        #[MapFrom('file_location')]
        private readonly string $fileLocation,
        #[MapFrom('file_size')]
        private readonly string $fileSize,
        #[MapFrom('file_size_bytes'), CastTo('int')]
        private readonly int $fileSizeBytes,
        #[MapFrom('last_modified')]
        private readonly string $lastModified,
        #[MapFrom('last_modified_timestamp'), CastTo('int')]
        private readonly int $lastModifiedTimestamp,
        private readonly array $actions,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getValue(string $field): mixed
    {
        return match ($field) {
            'id' => $this->id,
            'file_name' => $this->fileName,
            'file_location' => $this->fileLocation,
            'file_size' => $this->fileSize,
            'file_size_bytes' => $this->fileSizeBytes,
            'last_modified' => $this->lastModified,
            'last_modified_timestamp' => $this->lastModifiedTimestamp,
            'actions' => $this->actions,
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'file_name' => $this->fileName,
            'file_location' => $this->fileLocation,
            'file_size' => $this->fileSize,
            'file_size_bytes' => $this->fileSizeBytes,
            'last_modified' => $this->lastModified,
            'last_modified_timestamp' => $this->lastModifiedTimestamp,
            'actions' => $this->actions,
        ];
    }
}
