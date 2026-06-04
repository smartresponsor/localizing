<?php

declare(strict_types=1);

namespace App\ServiceInterface\Catalog;

use App\Dto\Catalog\LocaleCatalogMessageDto;

interface LocaleCatalogExporterInterface
{
    /** @param list<LocaleCatalogMessageDto> $messages */
    public function export(array $messages): int;
}
