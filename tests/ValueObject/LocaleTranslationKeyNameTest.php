<?php

declare(strict_types=1);

namespace App\Localizing\Tests\ValueObject;

use App\Localizing\Exception\LocaleInvalidTranslationKeyException;
use App\Localizing\ValueObject\LocaleTranslationKeyName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocaleTranslationKeyNameTest extends TestCase
{
    #[DataProvider('validKeys')]
    public function testAcceptsCanonicalTranslationKey(string $input, string $expected): void
    {
        $key = new LocaleTranslationKeyName($input);

        self::assertSame($expected, $key->value());
        self::assertSame($expected, (string) $key);
    }

    /** @return iterable<string, array{string, string}> */
    public static function validKeys(): iterable
    {
        yield 'flat' => ['title', 'title'];
        yield 'nested' => ['checkout.payment.title', 'checkout.payment.title'];
        yield 'trimmed' => ['  account_name  ', 'account_name'];
    }

    #[DataProvider('invalidKeys')]
    public function testRejectsInvalidTranslationKey(string $input): void
    {
        $this->expectException(LocaleInvalidTranslationKeyException::class);
        new LocaleTranslationKeyName($input);
    }

    /** @return iterable<string, array{string}> */
    public static function invalidKeys(): iterable
    {
        yield 'empty' => [''];
        yield 'uppercase' => ['Checkout.Title'];
        yield 'leading digit' => ['1checkout.title'];
        yield 'space' => ['checkout title'];
    }
}
