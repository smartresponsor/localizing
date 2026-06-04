<?php

declare(strict_types=1);

namespace App\ServiceInterface\Template;

use App\Dto\Template\LocaleTemplateContextDto;

interface LocaleTemplateContextProviderInterface
{
    /** @param list<string> $domains */
    public function provide(string $currentLocaleCode, array $domains = []): LocaleTemplateContextDto;
}
