<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Command;

use App\Localizing\Command\LocaleAuditCatalogsCommand;
use App\Localizing\Command\LocaleExportCatalogsCommand;
use App\Localizing\Command\LocaleNameCommand;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogExporterServiceInterface;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerServiceInterface;
use App\Localizing\ServiceInterface\LocaleCodeNameConverterServiceInterface;
use App\Localizing\ServiceInterface\Quality\LocaleCatalogAuditorServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\CommandTester;

final class LocaleCommandContractTest extends TestCase
{
    public function testAuditCommandReturnsSuccessWithoutErrorFindings(): void
    {
        $scanner = $this->createStub(LocaleCatalogScannerServiceInterface::class);
        $scanner->method('scan')->willReturn([]);
        $auditor = $this->createStub(LocaleCatalogAuditorServiceInterface::class);
        $auditor->method('audit')->willReturn([]);

        $tester = new CommandTester(new LocaleAuditCatalogsCommand($scanner, $auditor));
        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertStringContainsString('Messages: 0', $tester->getDisplay());
        self::assertStringContainsString('Findings: 0', $tester->getDisplay());
    }

    public function testAuditCommandReturnsFailureForErrorFinding(): void
    {
        $scanner = $this->createStub(LocaleCatalogScannerServiceInterface::class);
        $scanner->method('scan')->willReturn([]);
        $auditor = $this->createStub(LocaleCatalogAuditorServiceInterface::class);
        $auditor->method('audit')->willReturn([[
            'severity' => 'error',
            'code' => 'missing_default_locale',
            'domain' => 'messages',
            'key' => 'checkout.title',
            'locale' => null,
            'message' => 'Missing default locale translation.',
        ]]);

        $tester = new CommandTester(new LocaleAuditCatalogsCommand($scanner, $auditor));
        self::assertSame(Command::FAILURE, $tester->execute([]));
        self::assertStringContainsString('missing_default_locale', $tester->getDisplay());
    }

    public function testExportCommandReportsExportedCatalogCount(): void
    {
        $scanner = $this->createStub(LocaleCatalogScannerServiceInterface::class);
        $scanner->method('scan')->willReturn([]);
        $exporter = $this->createMock(LocaleCatalogExporterServiceInterface::class);
        $exporter->expects(self::once())->method('export')->with([])->willReturn(2);

        $tester = new CommandTester(new LocaleExportCatalogsCommand($scanner, $exporter));
        self::assertSame(Command::SUCCESS, $tester->execute([]));
        self::assertStringContainsString('Exported 2 catalog file(s) from 0 message(s).', $tester->getDisplay());
    }

    public function testLocaleNameCommandUsesConfiguredArguments(): void
    {
        $converter = $this->createMock(LocaleCodeNameConverterServiceInterface::class);
        $converter->expects(self::once())
            ->method('convertCodeToName')
            ->with('uk', 'en')
            ->willReturn('Ukrainian');

        $tester = new CommandTester(new LocaleNameCommand($converter));
        self::assertSame(Command::SUCCESS, $tester->execute([
            'code' => 'uk',
            'display-locale' => 'en',
        ]));
        self::assertStringContainsString('Ukrainian', $tester->getDisplay());
    }

    public function testLocaleNameCommandRejectsNonScalarArgumentValue(): void
    {
        $converter = $this->createStub(LocaleCodeNameConverterServiceInterface::class);
        $command = new LocaleNameCommand($converter);
        $input = new class extends ArrayInput {
            public function __construct()
            {
                parent::__construct([]);
            }

            public function getArgument(string $name): mixed
            {
                return ['uk'];
            }
        };

        $method = new \ReflectionMethod(LocaleNameCommand::class, 'stringArgument');

        $this->expectException(\InvalidArgumentException::class);
        $method->invoke($command, $input, 'code');
    }
}
