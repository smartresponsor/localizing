<?php

declare(strict_types=1);

namespace App\Command;

use App\ServiceInterface\Locale\LocaleCodeNameConverterInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'localizing:locale-name', description: 'Resolve a locale code to a localized display name.')]
final class LocaleNameCommand extends Command
{
    public function __construct(private readonly LocaleCodeNameConverterInterface $converter)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'Locale code, for example en or uk-UA')
            ->addArgument('display-locale', InputArgument::OPTIONAL, 'Locale used to display the name', 'en');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = (string) $input->getArgument('code');
        $displayLocale = (string) $input->getArgument('display-locale');
        $io->writeln($this->converter->convertCodeToName($code, $displayLocale));

        return Command::SUCCESS;
    }
}
