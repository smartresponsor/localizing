<?php

declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use App\ServiceInterface\Quality\LocaleCatalogAuditorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'localizing:audit-catalogs', description: 'Audit ecosystem translation catalogs.')]
final class LocaleAuditCatalogsCommand extends Command
{
    public function __construct(private readonly LocaleCatalogScannerInterface $scanner, private readonly LocaleCatalogAuditorInterface $auditor)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $messages = $this->scanner->scan();
        $findings = $this->auditor->audit($messages);

        $io->title('Localizing catalog audit');
        $io->writeln(sprintf('Messages: %d', count($messages)));
        $io->writeln(sprintf('Findings: %d', count($findings)));

        foreach ($findings as $finding) {
            $io->writeln(sprintf('[%s] %s %s.%s %s', $finding['severity'], $finding['code'], $finding['domain'], $finding['key'], $finding['message']));
        }

        return count(array_filter($findings, static fn (array $finding): bool => 'error' === $finding['severity'])) > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
