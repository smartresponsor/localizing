<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Catalog;

use App\Localizing\Service\Catalog\LocaleCatalogMessage;

interface LocaleCatalogExporterInterface
{
    /** @param list<LocaleCatalogMessage> $messages */
    public function export(array $messages): int;
}
