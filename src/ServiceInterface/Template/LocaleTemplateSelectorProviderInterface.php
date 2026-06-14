<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Template;

use App\Localizing\Dto\Template\LocaleTemplateSelectorOptionDto;

interface LocaleTemplateSelectorProviderInterface
{
    /** @return list<LocaleTemplateSelectorOptionDto> */
    public function provide(string $currentLocaleCode, ?string $displayLocaleCode = null): array;
}
