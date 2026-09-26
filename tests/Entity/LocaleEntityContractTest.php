<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Entity;

use App\Localizing\Entity\LocaleEntity;
use App\Localizing\Entity\LocaleFallbackEntity;
use App\Localizing\Entity\LocaleTerminologyEntryEntity;
use App\Localizing\Entity\LocaleTranslationAuditFindingEntity;
use App\Localizing\Entity\LocaleTranslationDomainEntity;
use App\Localizing\Entity\LocaleTranslationKeyEntity;
use App\Localizing\Entity\LocaleTranslationMessageEntity;
use PHPUnit\Framework\TestCase;

final class LocaleEntityContractTest extends TestCase
{
    public function testLocaleEntityExposesAndTransitionsState(): void
    {
        $locale = new LocaleEntity('uk', 'Ukrainian', false, 10);

        self::assertNull($locale->getId());
        self::assertSame('uk', $locale->getCode());
        self::assertSame('Ukrainian', $locale->getName());
        self::assertFalse($locale->isEnabled());
        self::assertSame(10, $locale->getPriority());

        $locale->enable();
        self::assertTrue($locale->isEnabled());
        $locale->disable();
        self::assertFalse($locale->isEnabled());
    }

    public function testFallbackAndDomainEntitiesExposeConstructorState(): void
    {
        $fallback = new LocaleFallbackEntity('uk-UA', 'uk', 1);
        self::assertNull($fallback->getId());
        self::assertSame('uk-UA', $fallback->getLocaleCode());
        self::assertSame('uk', $fallback->getFallbackLocaleCode());
        self::assertSame(1, $fallback->getPosition());

        $domain = new LocaleTranslationDomainEntity('messages', 'Checkout');
        self::assertNull($domain->getId());
        self::assertSame('messages', $domain->getName());
        self::assertSame('Checkout', $domain->getComponent());
    }

    public function testTerminologyAndTranslationKeyEntitiesExposeState(): void
    {
        $terminology = new LocaleTerminologyEntryEntity('cart', 'uk', 'кошик', 'Preferred noun');
        self::assertNull($terminology->getId());
        self::assertSame('cart', $terminology->getSourceTerm());
        self::assertSame('uk', $terminology->getLocaleCode());
        self::assertSame('кошик', $terminology->getApprovedTerm());
        self::assertSame('Preferred noun', $terminology->getNote());
        $terminology->update('корзина', null);
        self::assertSame('корзина', $terminology->getApprovedTerm());
        self::assertNull($terminology->getNote());

        $key = new LocaleTranslationKeyEntity('messages', 'checkout.submit', 'Checkout');
        self::assertNull($key->getId());
        self::assertSame('messages', $key->getDomainName());
        self::assertSame('checkout.submit', $key->getKeyName());
        self::assertSame('Checkout', $key->getComponent());
    }

    public function testAuditFindingAndTranslationMessageExposeState(): void
    {
        $finding = new LocaleTranslationAuditFindingEntity('error', 'missing', 'messages', 'checkout.title', 'uk', 'Missing translation');
        self::assertNull($finding->getId());
        self::assertSame('error', $finding->getSeverity());
        self::assertSame('missing', $finding->getCode());
        self::assertSame('messages', $finding->getDomainName());
        self::assertSame('checkout.title', $finding->getKeyName());
        self::assertSame('uk', $finding->getLocaleCode());
        self::assertSame('Missing translation', $finding->getMessage());

        $message = new LocaleTranslationMessageEntity('uk', 'messages', 'checkout.submit', 'Сплатити');
        self::assertNull($message->getId());
        self::assertSame('uk', $message->getLocaleCode());
        self::assertSame('messages', $message->getDomainName());
        self::assertSame('checkout.submit', $message->getKeyName());
        self::assertSame('Сплатити', $message->getMessage());
        $message->updateMessage('Оплатити');
        self::assertSame('Оплатити', $message->getMessage());
    }
}
