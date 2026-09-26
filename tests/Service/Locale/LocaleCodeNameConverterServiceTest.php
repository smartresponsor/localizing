<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Service\Locale;

use App\Localizing\Service\LocaleCodeNameConverterService;
use PHPUnit\Framework\TestCase;

final class LocaleCodeNameConverterServiceTest extends TestCase
{
    public function testConvertsCodeToLocalizedDisplayName(): void
    {
        $converter = new LocaleCodeNameConverterService();

        self::assertSame('English', $converter->convertCodeToName('en', 'en'));
        self::assertSame('Ukrainian', $converter->convertCodeToName('uk', 'en'));
    }

    public function testConvertsDisplayNameBackToCode(): void
    {
        $converter = new LocaleCodeNameConverterService();

        self::assertSame('en', $converter->convertNameToCode('English', 'en'));
        self::assertSame('uk', $converter->convertNameToCode('Ukrainian', 'en'));
    }

    public function testRejectsUnknownDisplayName(): void
    {
        $converter = new LocaleCodeNameConverterService();

        $this->expectException(\InvalidArgumentException::class);
        $converter->convertNameToCode('Not a real locale', 'en');
    }

    public function testRejectsUnknownLocaleCode(): void
    {
        $converter = new LocaleCodeNameConverterService();

        $this->expectException(\InvalidArgumentException::class);
        $converter->convertCodeToName('zz-ZZ', 'en');
    }
}
