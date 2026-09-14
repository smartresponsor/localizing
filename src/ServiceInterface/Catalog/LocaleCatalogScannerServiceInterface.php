<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Catalog;

use App\Localizing\DTO\Catalog\LocaleCatalogMessageDTO;

/**
 * Discovers translation catalog messages from configured localization sources.
 */
interface LocaleCatalogScannerServiceInterface
{
    /**
     * Returns every discovered catalog message as a normalized transport object.
     *
     * @return list<LocaleCatalogMessageDTO>
     */
    public function scan(): array;
}
