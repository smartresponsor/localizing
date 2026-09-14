<?php

declare(strict_types=1);

namespace App\Localizing\Tests\ValueObject;

use App\Localizing\Exception\LocaleInvalidCodeException;
use App\Localizing\ValueObject\LocaleCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocaleCodeTest extends TestCase
{
    #[DataProvider('validCodes')]
    public function testNormalizesAndExposesLocaleCode(string $input, string $expected, string $language): void
    {
        $code = new LocaleCode($input);

        self::assertSame($expected, $code->value());
        self::assertSame($language, $code->language());
        self::assertSame($expected, (string) $code);
    }

    /** @return iterable<string, array{string, string, string}> */
    public static function validCodes(): iterable
    {
        yield 'language' => ['uk', 'uk', 'uk'];
        yield 'regional hyphen' => ['en-US', 'en-US', 'en'];
        yield 'regional underscore' => ['en_US', 'en-US', 'en'];
        yield 'trimmed' => ['  fr  ', 'fr', 'fr'];
    }

    #[DataProvider('invalidCodes')]
    public function testRejectsInvalidLocaleCode(string $input): void
    {
        $this->expectException(LocaleInvalidCodeException::class);
        new LocaleCode($input);
    }

    /** @return iterable<string, array{string}> */
    public static function invalidCodes(): iterable
    {
        yield 'empty' => [''];
        yield 'uppercase language' => ['EN'];
        yield 'lowercase region' => ['en-us'];
        yield 'too long' => ['english'];
    }
}
