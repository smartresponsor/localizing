param(
    [string]$RepoRoot = (Get-Location).Path,
    [string]$PatchRoot = $PSScriptRoot + '\..\..'
)

$ErrorActionPreference = 'Stop'

$deleteFiles = @(
    'src\DataFixtures\LocalizingDemoFixtures.php',

    'src\Entity\TerminologyEntry.php',

    'src\Entity\TranslationAuditFinding.php',

    'src\Entity\TranslationDomain.php',

    'src\Entity\TranslationKey.php',

    'src\Entity\TranslationMessage.php',

    'src\Exception\InvalidLocaleCodeException.php',

    'src\Exception\InvalidTranslationKeyException.php',

    'src\Repository\TerminologyEntryRepository.php',

    'src\Repository\TranslationAuditFindingRepository.php',

    'src\Repository\TranslationDomainRepository.php',

    'src\Repository\TranslationKeyRepository.php',

    'src\Repository\TranslationMessageRepository.php',

    'src\Service\Catalog\LocaleCatalogMessage.php',

    'src\ValueObject\TranslationKeyName.php',

    'tests\LocalizingDemoFixturesContractTest.php',
)

foreach ($relativePath in $deleteFiles) {
    $target = Join-Path $RepoRoot $relativePath
    if (Test-Path -LiteralPath $target -PathType Leaf) {
        Remove-Item -LiteralPath $target -Force
    }
}

$patchRootFull = (Resolve-Path -LiteralPath $PatchRoot).Path.TrimEnd('\')
$skip = @('DELETE_FILES.txt', 'PATCH_SUMMARY.md', 'tools\patch\apply-localizing-w01-canonical-naming.ps1')

Get-ChildItem -LiteralPath $patchRootFull -Recurse -File | ForEach-Object {
    $relative = $_.FullName.Substring($patchRootFull.Length).TrimStart('\')
    if ($relative -in $skip) {
        return
    }

    $target = Join-Path $RepoRoot $relative
    $targetDir = Split-Path -Parent $target
    if (-not (Test-Path -LiteralPath $targetDir)) {
        New-Item -ItemType Directory -Path $targetDir | Out-Null
    }
    Copy-Item -LiteralPath $_.FullName -Destination $target -Force
}

Write-Host 'Applied Localizing W01 canonical naming patch.'
