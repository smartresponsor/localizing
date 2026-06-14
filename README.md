# Localizing

Localizing is the internationalization and translation management component of the Smart Responsor platform. It governs the locale registry, fallback policies, missing translation tracking, translation glossaries, and catalog imports/exports.

This module is **not** a synchronous, real-time translation server. It compiles and exports validated translation catalogs that host Symfony applications read at runtime.

## Current Posture

### What the component already does
- Defines translation fallback chains and locale registries.
- Provides tools for auditing translation files and checking translation key/placeholder consistency.
- Exports Symfony-compatible translation catalogs.
- Imports translation assets from legacy repositories.

### What this repository does not claim yet
- Real-time user session translation caching.
- Direct automated translation API wrappers (e.g. DeepL or Google Translate integration).

## Runtime Surface & Entrypoints

The component exposes console commands for catalog verification and export:
- `bin/console localizing:audit-catalogs` - Audits catalogs for validation and missing strings.
- `bin/console localizing:export-catalogs` - Generates compiled catalogs for consumption.
- `src/` - Core domain services, validator constraints, and lifecycle events.

## Local Setup

Install dependencies:
```bash
composer install
```

Run translation audits:
```bash
composer run localizing:audit
```

## Local Composer Path Installation

To include Localizing in your Symfony project:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../Localizing",
      "options": {
        "symlink": true
      }
    }
  ],
  "require": {
    "localizing/locale": "*@dev"
  }
}
```

## Documentation Map

- [Localizing Canon Definition](docs/architecture/localizing-canon.adoc)
- [Namespace Canon](docs/architecture/localizing-namespace-canon.adoc)
- [Template Output Contract](docs/architecture/localizing-template-output-contract.adoc)
- [Legacy Salvage Report](docs/architecture/localizing-legacy-salvage-report.adoc)
