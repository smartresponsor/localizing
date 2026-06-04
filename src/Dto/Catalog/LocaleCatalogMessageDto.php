<?php

declare(strict_types=1);

namespace App\Dto\Catalog;

final readonly class LocaleCatalogMessageDto
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
