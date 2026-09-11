# CMCP orchestration journal

## engine-20260911142429-localizing-31f3b4

### Iteration 1 — reconnaissance and baseline

- Read `AGENTS.md`, `README.md`, `composer.json`, architecture docs, Symfony configuration, routes, delivery manifest, source/test topology, and current Git state.
- Read mandatory dependency contour contracts from Objecting, Cruding, Viewing, and Interfacing.
- Read Canonization rules Canon000, 001, 002, 005, 007, 008, 010, 017, 018, 019, 021-026, 029, 030, 032, 033, and 038 plus the architecture guard matrix; inspected matching Gating implementations for core identity/topology checks.
- Existing changes preserved: `config/reference.php` is modified and `.gating/` is untracked; neither is treated as Localizing implementation work.
- Baseline findings: standalone dependency contour is incomplete; Symfony floor is below Canon026; `composer.prod.json` is absent; PHP quality execution/config is incomplete; `delivery/manifest.yaml` contains stale/nonexistent artifacts; an obsolete generic `locale.index` route remains.
- Canon mapping: `localizing/locale` maps to `App\\Localizing\\ => src/` and the `Locale*` subject prefix. Existing source namespace is canonical under Canon018; local docs claiming root `App\\Entity\\Locale` are stale.
- Market/maturity scan: mature localization systems emphasize locale/fallback governance, terminology/glossary consistency, placeholder QA, catalog lifecycle and validated export. Localizing should keep runtime translation in Symfony Translator rather than becoming a synchronous translation server.
- RC-critical workstream: package/runtime integrity, dependency baseline, quality gates, route ownership, catalog/export correctness, and factual documentation.
- Growth workstream: translation memory, review workflow, pseudolocalization, screenshots/in-context editing, provider/AI integrations, and richer bridge UX.
- Planned gates: Composer validation/install, syntax, PHP-CS-Fixer, PHPStan, PHPUnit, catalog audit, Symfony/Doctrine checks where executable, and final Git verification.

### Iteration 2 — material implementation

- Canonical standalone dependency and Symfony 8.1 baseline migration started in `composer.json`.
- Production manifest, standard quality-tool configuration, stale delivery metadata cleanup, and generic route cleanup selected as bounded implementation work.

### Iteration 3 — verification and fix

- `composer validate --strict`, PHP syntax (64 files), PHP-CS-Fixer check, PHPStan max level, PHPUnit (2 tests / 164 assertions), catalog audit (12 messages / 0 findings), Symfony container lint, YAML lint (16 files), and Doctrine mapping validation all reached green after targeted fixes.
- Hardened admin JSON boundaries by validating decoded scalar shapes before casts instead of suppressing PHPStan findings.
- Fixed standalone `bin/console` from stale `App\\Kernel` to `App\\Localizing\\Kernel`.
- Completed Objecting standalone integration: registered `ObjectBundle` and Objecting embeddable Doctrine metadata while retaining Localizing entity/migration ownership.
- Added executable Doctrine schema-parity/currentness scripts; materialized the configured migrations root without inventing historical migrations; removed the stale manifest claim for a nonexistent migration.
- Gating now executes from the canonical sibling policy root; Canon021-030 and Canon032-036 pass, including standalone dependencies, path symlinks, prod packaging, Symfony 8.1 baseline, quality tooling, schema parity, bundle registration, Composer parity, and gitignore baseline.

### Iteration 4 — debt closure and integration

- Remaining Gating failures are structural legacy debt: `src/Dto`/`*Dto`, `src/Lifecycle`, premature `Service/Locale` and `ServiceInterface/Locale`, Resolver under Service, `SupportedLocale` subject-prefix drift, ServiceInterface naming, two route YAML filenames, route `{code}` grammar, tracked generated `config/reference.php`, and existing mutation-safety findings.
- Verified that the repository patch capability rejects even content-preserving rename/copy patches (`Rename and copy patches are not allowed`). The task capability envelope also forbids destructive operations, so rename/remove-dependent repairs are not safely executable in this run.
- `config/reference.php` and `.gating/` were dirty before this task and remain deliberately excluded from task commits.
- RC packaging decision: commit only bounded, verified Localizing implementation/config/documentation changes; do not claim full Canonization RC while structural rename/remove blockers remain.

### Iteration 5 — final acceptance and handoff

- Final acceptance reconfirmed `composer validate --strict`, PHPStan max level, and PHPUnit green after all executable fixes; earlier final-state passes also include syntax, PHP-CS-Fixer, catalog audit, Symfony container/YAML lint, Doctrine mapping validation, and migration currentness.
- Gating remains intentionally red only on unresolved structural/policy blockers that require rename/remove operations or pre-existing tracked/generated-state cleanup; Canon030 and Canon034 remain green after this task's fixes.
- Safe Git integration was attempted but blocked by console-MCP because the current protected `master` is dirty and already one commit ahead of `origin/master`; feature-branch switching is guard-blocked while dirty. No commit or push was forced, and pre-existing `config/reference.php` / `.gating/` state was not staged or overwritten.
- Final state is a materially improved, verified bounded implementation, but not a full Canonization RC. The next authorized run must permit content-preserving renames/removals and start from a clean/safely branchable Git state to close the remaining topology failures.

### Continuation — route grammar closure

- Reworked locale fallback/enable/disable routes to canonical operation-before-final-identifier grammar using `{slug}` for locale codes and `{id}` for fallback deletion.
- Simplified fallback deletion to its unique primary key, removing the unnecessary locale-code dynamic segment.
- Updated API/Admin controller argument names and lookups to match the new route variables while preserving locale-code semantics.
- Gating confirms `route.path_segment_separation` now passes for all 32 routes.
- Replaced broad `Remove-Item -Recurse -Force` temp cleanup in the bootstrap patch tool with a prefix-guarded `System.IO.Directory::Delete` call; `mutation.safety_firewall` now passes.
- Post-continuation regression remains green: PHPStan 0 errors, PHPUnit 2 tests / 164 assertions, PHP-CS-Fixer 0 files needing fixes.
- Remaining Gating state: 9 failed, 1 warning, 12 skipped. All remaining failed rules require rename/remove/untrack operations not permitted by the current mutation capability.
