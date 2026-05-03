<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Catalog;

use App\Localizing\Service\Catalog\LocaleCatalogMessage;

interface LocaleCatalogScannerInterface
{
    /** @return list<LocaleCatalogMessage> */
    public function scan(): array;
}
