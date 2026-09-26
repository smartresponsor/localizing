<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Quality;

use App\Localizing\DTO\Catalog\LocaleCatalogMessageDTO;

/**
 * Evaluates normalized catalog messages against Localizing quality rules.
 */
interface LocaleCatalogAuditorServiceInterface
{
    /**
     * Produces structured findings for missing, unsupported, or invalid catalog content.
     *
     * @param list<LocaleCatalogMessageDTO> $messages
     *
     * @return list<array{severity:string, code:string, domain:string, key:string, locale:?string, message:string}>
     */
    public function audit(array $messages): array;
}
