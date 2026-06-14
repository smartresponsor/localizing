<?php

declare(strict_types=1);

namespace App\Localizing\DataFixtures;

use App\Localizing\Entity\LocaleEntity;
use App\Localizing\Entity\LocaleFallbackEntity;
use App\Localizing\Entity\LocaleTerminologyEntryEntity;
use App\Localizing\Entity\LocaleTranslationAuditFindingEntity;
use App\Localizing\Entity\LocaleTranslationDomainEntity;
use App\Localizing\Entity\LocaleTranslationKeyEntity;
use App\Localizing\Entity\LocaleTranslationMessageEntity;
use Doctrine\Persistence\ObjectManager;

final class LocaleDemoFixtures
{
    public function load(ObjectManager $manager): void
    {
        $locales = [
            new LocaleEntity('en_US', 'English (US)', true, 1),
            new LocaleEntity('en_GB', 'English (UK)', true, 2),
            new LocaleEntity('uk_UA', 'Ukrainian', true, 3),
            new LocaleEntity('de_DE', 'German', false, 4),
        ];

        foreach ($locales as $locale) {
            $manager->persist($locale);
        }

        foreach ([
            ['storefront', 'cataloging', ['hero_title', 'hero_subtitle', 'featured_cta']],
            ['checkout', 'ordering', ['cart_title', 'checkout_button', 'payment_hint']],
            ['emails', 'messaging', ['order_subject', 'shipment_subject', 'support_subject']],
        ] as [$domainName, $component, $keys]) {
            $domain = new LocaleTranslationDomainEntity($domainName, $component);
            $manager->persist($domain);

            foreach ($keys as $keyName) {
                $translationKey = new LocaleTranslationKeyEntity($domainName, $keyName, $component);
                $manager->persist($translationKey);

                foreach (['en_US', 'en_GB', 'uk_UA'] as $localeCode) {
                    $manager->persist(new LocaleTranslationMessageEntity(
                        $localeCode,
                        $domainName,
                        $keyName,
                        match ($domainName) {
                            'storefront' => match ($keyName) {
                                'hero_title' => 'Shop premium essentials for every room',
                                'hero_subtitle' => 'Fast delivery, easy returns, and trusted sellers.',
                                default => 'Browse curated collections and seasonal offers.',
                            },
                            'checkout' => match ($keyName) {
                                'cart_title' => 'Your cart',
                                'checkout_button' => 'Proceed to checkout',
                                default => 'Secure payment and transparent shipping totals.',
                            },
                            default => match ($keyName) {
                                'order_subject' => 'Your order has been confirmed',
                                'shipment_subject' => 'Your order is on the way',
                                default => 'Customer support replied to your request.',
                            },
                        },
                    ));
                }
            }
        }

        foreach ([
            ['uk_UA', 'en_US', 1],
            ['en_GB', 'en_US', 1],
            ['de_DE', 'en_US', 1],
        ] as [$localeCode, $fallbackLocaleCode, $position]) {
            $manager->persist(new LocaleFallbackEntity($localeCode, $fallbackLocaleCode, $position));
        }

        foreach (range(1, 4) as $index) {
            $manager->persist(new LocaleTerminologyEntryEntity(
                sprintf('store term %02d', $index),
                $this->pick(['en_US', 'uk_UA', 'de_DE'], $index),
                sprintf('approved term %02d', $index),
                $this->pick([
                    'Used across product pages, checkout copy, and notification emails.',
                    'Keep terminology consistent between storefront and support flows.',
                    'Preferred wording for commerce-facing translations.',
                ], $index),
            ));

            $manager->persist(new LocaleTranslationAuditFindingEntity(
                $this->pick(['info', 'warning', 'critical'], $index),
                sprintf('LOC-%03d', $index),
                $this->pick(['storefront', 'checkout', 'emails'], $index),
                $this->pick(['hero_title', 'cart_title', 'order_subject'], $index),
                $this->pick(['en_US', 'uk_UA', null], $index),
                $this->pick([
                    'Missing localized storefront headline.',
                    'Fallback wording is visible during checkout.',
                    'Notification subject should be aligned with branding.',
                ], $index),
            ));
        }

        $manager->flush();
    }

    /**
     * @template T
     *
     * @param non-empty-list<T> $values
     *
     * @return T
     */
    private function pick(array $values, int $seed): mixed
    {
        return $values[($seed - 1) % count($values)];
    }
}
