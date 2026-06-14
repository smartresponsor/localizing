<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Catalog;

use App\Localizing\Dto\Catalog\LocaleCatalogMessageDto;

interface LocaleCatalogExporterInterface
{
    /** @param list<LocaleCatalogMessageDto> $messages */
    public function export(array $messages): int;
}
