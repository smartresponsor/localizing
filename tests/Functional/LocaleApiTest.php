<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LocaleApiTest extends WebTestCase
{
    public function testLocaleListEndpointReturnsConfiguredLocales(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/locale');

        self::assertResponseIsSuccessful();
        self::assertResponseFormatSame('json');

        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);
        self::assertSame('en', $payload['default'] ?? null);
        $locales = $payload['locales'] ?? null;
        self::assertIsArray($locales);
        self::assertNotEmpty($locales);
        $firstLocale = $locales[0] ?? null;
        self::assertIsArray($firstLocale);
        self::assertSame('en', $firstLocale['code'] ?? null);
        self::assertTrue($firstLocale['default'] ?? false);
    }

    public function testFallbackEndpointReturnsDeterministicChain(): void
    {
        $client = self::createClient();
        $client->request('GET', '/api/locale/fallback/uk-UA');

        self::assertResponseIsSuccessful();
        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);
        self::assertSame('uk-UA', $payload['locale'] ?? null);
        $fallbackChain = $payload['fallback_chain'] ?? null;
        self::assertIsArray($fallbackChain);
        self::assertSame(['uk-UA', 'uk', 'en'], $fallbackChain);
    }
}
