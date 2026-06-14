# Localizing entity-first migration retirement

## Scope

Current platform slice: `www-clean-20260610-170157(1).zip`.
Old monolith donor: `Entity-src(6).zip`, specifically `Entity/Locale`.

## Retired schema-first sources

- `Localizing/migrations/**`

The migration described tables that are already represented by current Doctrine entities:

- `locale_locale` -> `LocaleEntity`
- `locale_fallback` -> `LocaleFallbackEntity`
- `locale_translation_domain` -> `LocaleTranslationDomainEntity`
- `locale_translation_key` -> `LocaleTranslationKeyEntity`
- `locale_translation_message` -> `LocaleTranslationMessageEntity`
- `locale_terminology_entry` -> `LocaleTerminologyEntryEntity`
- `locale_translation_audit_finding` -> `LocaleTranslationAuditFindingEntity`

## Old monolith reconciliation

Old classes reviewed:

- `Entity/Locale/Locale.php`
- `Entity/Locale/LocaleEn.php`
- `Entity/Locale/SupportedLocale.php`

Resolution:

- `Locale` maps to existing `LocaleEntity`.
- `LocaleEn` is not carried forward as a locale-specific entity; localized strings are normalized through `LocaleTranslationMessageEntity`.
- `SupportedLocale` is restored as `App\Localizing\ValueObject\SupportedLocale`, not as a Doctrine entity, because it is a static semantic catalog/constants object rather than persisted aggregate state.

## Objecting/system fields

The retired Localizing migration did not define generic audit/state columns like `created_at`, `updated_at`, `deleted_at`, `status`, `slug`, or title aliases.
Therefore this patch does not inject Objecting embeddables into existing Localizing entities just to create new schema. Localizing keeps only business fields already represented by the locale registry/catalog model.

## Repository contracts

Repository interfaces were added under `src/RepositoryInterface` and concrete repositories now implement them. This keeps Localizing aligned with the entity-first/mirror-interface pattern without introducing migration coupling.
