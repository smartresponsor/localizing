<?php

declare(strict_types=1);

$projectDir = dirname(__DIR__);
$functionalTest = $projectDir.'/tests/Functional/LocaleApiTest.php';
$testSource = is_file($functionalTest) ? (string) file_get_contents($functionalTest) : '';

$surfaces = [
    'functional' => [
        'route:localizing_api_locale_list' => 'testLocaleListEndpointReturnsConfiguredLocales',
        'route:localizing_api_locale_fallback' => 'testFallbackEndpointReturnsDeterministicChain',
    ],
    'behavioral' => [
        'workflow:locale_discovery' => 'testLocaleListEndpointReturnsConfiguredLocales',
        'workflow:locale_fallback_resolution' => 'testFallbackEndpointReturnsDeterministicChain',
    ],
    'ui' => [],
    'critical' => [
        'workflow:locale_fallback_resolution' => 'testFallbackEndpointReturnsDeterministicChain',
    ],
];

$dimensions = [];
foreach ($surfaces as $dimension => $inventory) {
    $eligible = array_keys($inventory);
    $covered = [];

    foreach ($inventory as $identifier => $testMethod) {
        if (str_contains($testSource, 'function '.$testMethod.'(')) {
            $covered[] = $identifier;
        }
    }

    $dimensions[$dimension] = [
        'eligible' => $eligible,
        'covered' => $covered,
    ];
}

$evidence = [
    'schema' => 'behavioral-ui-coverage-v2',
    'generatedAt' => (new DateTimeImmutable())->format(DateTimeInterface::ATOM),
    'producer' => [
        'kind' => 'repository_script',
        'script' => 'test:behavioral-coverage',
    ],
    'dimensions' => $dimensions,
];

$outputDirectory = $projectDir.'/var/coverage';
if (!is_dir($outputDirectory) && !mkdir($outputDirectory, 0777, true) && !is_dir($outputDirectory)) {
    fwrite(STDERR, "Unable to create coverage output directory.\n");
    exit(1);
}

$json = json_encode($evidence, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR).PHP_EOL;
if (false === file_put_contents($outputDirectory.'/behavioral-ui.json', $json)) {
    fwrite(STDERR, "Unable to write behavioral/UI coverage evidence.\n");
    exit(1);
}

foreach ($dimensions as $dimension => $inventory) {
    printf(
        "%s: %d/%d\n",
        $dimension,
        count($inventory['covered']),
        count($inventory['eligible']),
    );
}
