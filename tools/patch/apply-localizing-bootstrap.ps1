param(
    [Parameter(Mandatory = $true)][string]$ProjectRoot,
    [Parameter(Mandatory = $true)][string]$PatchZip
)

$ErrorActionPreference = 'Stop'

function Normalize-FullPath([string]$PathValue) {
    return [System.IO.Path]::GetFullPath($PathValue)
}

if (-not (Test-Path -LiteralPath $PatchZip)) {
    throw "Patch zip not found: $PatchZip"
}

$projectFull = Normalize-FullPath $ProjectRoot
$temp = Join-Path ([System.IO.Path]::GetTempPath()) ("localizing_patch_" + [Guid]::NewGuid().ToString('N'))
New-Item -ItemType Directory -Path $temp | Out-Null
try {
    Expand-Archive -LiteralPath $PatchZip -DestinationPath $temp -Force
    Get-ChildItem -LiteralPath $temp -Recurse -File | ForEach-Object {
        $relative = [System.IO.Path]::GetRelativePath($temp, $_.FullName)
        $target = Join-Path $projectFull $relative
        $targetDir = Split-Path -Parent $target
        if (-not (Test-Path -LiteralPath $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
        }
        Copy-Item -LiteralPath $_.FullName -Destination $target -Force
        Write-Host "Overlay touched file: $relative"
    }
}
finally {
    Remove-Item -LiteralPath $temp -Recurse -Force -ErrorAction SilentlyContinue
}
