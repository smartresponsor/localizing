<?php

declare(strict_types=1);

namespace App\Localizing\Service\Catalog;

final readonly class LocaleCatalogMessage
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
