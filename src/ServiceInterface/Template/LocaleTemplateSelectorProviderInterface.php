<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Template;

use App\Localizing\ValueObject\LocaleTemplateSelectorOption;

interface LocaleTemplateSelectorProviderInterface
{
    /** @return list<LocaleTemplateSelectorOption> */
    public function provide(string $currentLocaleCode, ?string $displayLocaleCode = null): array;
}
