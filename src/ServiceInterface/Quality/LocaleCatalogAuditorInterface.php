<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Quality;

use App\Localizing\Service\Catalog\LocaleCatalogMessage;

interface LocaleCatalogAuditorInterface
{
    /**
     * @param list<LocaleCatalogMessage> $messages
     *
     * @return list<array{severity:string, code:string, domain:string, key:string, locale:?string, message:string}>
     */
    public function audit(array $messages): array;
}
