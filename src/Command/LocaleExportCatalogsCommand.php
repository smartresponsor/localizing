<?php

declare(strict_types=1);

namespace App\Localizing\Command;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogExporterServiceInterface;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'localizing:export-catalogs', description: 'Export validated catalogs for Symfony Translator runtime consumption.')]
/**
 * Exports discovered translation messages into runtime-ready Symfony catalog files.
 */
final class LocaleExportCatalogsCommand extends Command
{
    public function __construct(private readonly LocaleCatalogScannerServiceInterface $scanner, private readonly LocaleCatalogExporterServiceInterface $exporter)
    {
        parent::__construct();
    }

    /**
     * Scans source catalogs, exports runtime files, and reports the exported file count.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $messages = $this->scanner->scan();
        $files = $this->exporter->export($messages);
        $io->success(sprintf('Exported %d catalog file(s) from %d message(s).', $files, count($messages)));

        return Command::SUCCESS;
    }
}
