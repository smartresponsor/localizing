<?php

declare(strict_types=1);

namespace App\Localizing\ServiceInterface\Template;

use App\Localizing\Dto\Template\LocaleTemplateContextDto;

interface LocaleTemplateContextProviderInterface
{
    /** @param list<string> $domains */
    public function provide(string $currentLocaleCode, array $domains = []): LocaleTemplateContextDto;
}
