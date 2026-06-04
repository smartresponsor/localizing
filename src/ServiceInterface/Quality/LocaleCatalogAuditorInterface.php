<?php

declare(strict_types=1);

namespace App\ServiceInterface\Quality;

use App\Dto\Catalog\LocaleCatalogMessageDto;

interface LocaleCatalogAuditorInterface
{
    /**
     * @param list<LocaleCatalogMessageDto> $messages
     *
     * @return list<array{severity:string, code:string, domain:string, key:string, locale:?string, message:string}>
     */
    public function audit(array $messages): array;
}
