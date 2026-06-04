<?php

declare(strict_types=1);

namespace App\ServiceInterface\Catalog;

use App\Dto\Catalog\LocaleCatalogMessageDto;

interface LocaleCatalogScannerInterface
{
    /** @return list<LocaleCatalogMessageDto> */
    public function scan(): array;
}
