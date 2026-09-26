<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Service\Catalog;

use App\Localizing\DTO\Catalog\LocaleCatalogMessageDTO;
use App\Localizing\Service\Catalog\LocaleCatalogExporterService;
use App\Localizing\Service\Catalog\LocaleCatalogScannerService;
use App\Localizing\Service\LocaleRegistryService;
use App\Localizing\Service\Quality\LocaleCatalogAuditorService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class LocaleCatalogPipelineTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir().'/localizing-test-'.bin2hex(random_bytes(6));
        (new Filesystem())->mkdir($this->workspace.'/catalogs');
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->workspace);
    }

    public function testScannerFlattensYamlAndPipelineExportsNestedCatalogs(): void
    {
        file_put_contents($this->workspace.'/catalogs/messages.en.yaml', Yaml::dump([
            'checkout' => ['title' => 'Checkout', 'submit' => 'Pay'],
        ]));
        file_put_contents($this->workspace.'/catalogs/messages.uk.yaml', Yaml::dump([
            'checkout' => ['title' => 'Оформлення', 'submit' => 'Сплатити'],
        ]));

        $scanner = new LocaleCatalogScannerService($this->workspace.'/catalogs');
        $messages = $scanner->scan();

        self::assertCount(4, $messages);

        $auditor = new LocaleCatalogAuditorService(new LocaleRegistryService(['en', 'uk'], 'en'));
        self::assertSame([], $auditor->audit($messages));

        $exporter = new LocaleCatalogExporterService($this->workspace.'/export');
        self::assertSame(0, $exporter->export([]));
        self::assertSame(2, $exporter->export($messages));
        self::assertSame(
            ['checkout' => ['submit' => 'Pay', 'title' => 'Checkout']],
            Yaml::parseFile($this->workspace.'/export/messages.en.yaml'),
        );
    }

    public function testExporterReplacesScalarPrefixWithNestedCatalogStructure(): void
    {
        $messages = [
            new LocaleCatalogMessageDTO('en', 'messages', 'foo', 'scalar', 'memory'),
            new LocaleCatalogMessageDTO('en', 'messages', 'foo.bar', 'nested', 'memory'),
            new LocaleCatalogMessageDTO('en', 'messages', 'foo.baz', 'sibling', 'memory'),
        ];
        $exporter = new LocaleCatalogExporterService($this->workspace.'/collision-export', new Filesystem());
        $unflatten = new \ReflectionMethod(LocaleCatalogExporterService::class, 'unflatten');

        self::assertSame([], $unflatten->invoke($exporter, []));
        self::assertSame(1, $exporter->export($messages));
        self::assertSame(
            ['foo' => ['bar' => 'nested', 'baz' => 'sibling']],
            Yaml::parseFile($this->workspace.'/collision-export/messages.en.yaml'),
        );
    }

    public function testScannerIgnoresNonArrayCatalogsAndNonScalarLeaves(): void
    {
        file_put_contents($this->workspace.'/catalogs/scalar.en.yaml', "plain scalar\n");
        file_put_contents($this->workspace.'/catalogs/messages.en.yaml', "ignored: null\nkept: value\n");
        file_put_contents($this->workspace.'/catalogs/en.yaml', "defaulted: value\n");

        $messages = (new LocaleCatalogScannerService($this->workspace.'/catalogs'))->scan();

        self::assertCount(2, $messages);
        $byKey = [];
        foreach ($messages as $message) {
            $byKey[$message->keyName] = $message;
        }
        self::assertSame('messages', $byKey['defaulted']->domainName);
        self::assertSame('en', $byKey['defaulted']->localeCode);
        self::assertSame('value', $byKey['defaulted']->message);
        self::assertSame('value', $byKey['kept']->message);
    }

    public function testScannerFlattenHandlesEmptyInput(): void
    {
        $scanner = new LocaleCatalogScannerService($this->workspace.'/catalogs');
        $flatten = new \ReflectionMethod(LocaleCatalogScannerService::class, 'flatten');

        self::assertSame([], $flatten->invoke($scanner, []));
    }

    public function testScannerReturnsEmptyListForMissingDirectory(): void
    {
        self::assertSame([], (new LocaleCatalogScannerService($this->workspace.'/missing'))->scan());
    }

    public function testAuditorReportsUnsupportedEmptyAndMissingDefaultLocale(): void
    {
        $messages = [
            new LocaleCatalogMessageDTO('uk', 'messages', 'checkout.title', '', 'memory'),
            new LocaleCatalogMessageDTO('pl', 'messages', 'checkout.submit', 'Zapłać', 'memory'),
        ];
        $auditor = new LocaleCatalogAuditorService(new LocaleRegistryService(['en', 'uk'], 'en'));

        self::assertSame([], $auditor->audit([]));

        $findings = $auditor->audit($messages);
        $codes = array_column($findings, 'code');

        self::assertContains('empty_message', $codes);
        self::assertContains('unsupported_locale', $codes);
        self::assertSame(2, count(array_filter($codes, static fn (string $code): bool => 'missing_default_locale' === $code)));
    }
}
