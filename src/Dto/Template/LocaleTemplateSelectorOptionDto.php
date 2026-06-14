<?php

declare(strict_types=1);

namespace App\Localizing\Dto\Template;

final readonly class LocaleTemplateSelectorOptionDto
{
    public function __construct(
        public string $code,
        public string $nameEntity,
        public string $nativeName,
        public bool $current,
        public bool $default,
    ) {
    }

    /** @return array{code: string, name: string, nativeName: string, current: bool, default: bool} */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'nameEntity' => $this->nameEntity,
            'nativeName' => $this->nativeName,
            'current' => $this->current,
            'default' => $this->default,
        ];
    }
}
