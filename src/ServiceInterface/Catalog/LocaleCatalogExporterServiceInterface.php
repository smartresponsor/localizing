<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Catalog;

use App\Localizing\DTO\Catalog\LocaleCatalogMessageDTO;

/**
 * Writes normalized localization messages into runtime translation catalog files.
 */
interface LocaleCatalogExporterServiceInterface
{
    /**
     * Exports the supplied messages and returns the number of catalog files written.
     *
     * @param list<LocaleCatalogMessageDTO> $messages
     */
    public function export(array $messages): int;
}
