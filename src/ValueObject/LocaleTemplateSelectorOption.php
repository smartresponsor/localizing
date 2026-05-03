<?php

declare(strict_types=1);

namespace App\Localizing\ValueObject;

final readonly class LocaleTemplateSelectorOption
{
    public function __construct(
        public string $code,
        public string $name,
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
            'name' => $this->name,
            'nativeName' => $this->nativeName,
            'current' => $this->current,
            'default' => $this->default,
        ];
    }
}
