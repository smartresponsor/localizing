<?php

declare(strict_types=1);

namespace App\Localizing\DTO\Catalog;

final readonly class LocaleCatalogMessageDTO
{
    public function __construct(
        public string $localeCode,
        public string $domainName,
        public string $keyName,
        public string $message,
        public string $sourcePath,
    ) {
    }
}
