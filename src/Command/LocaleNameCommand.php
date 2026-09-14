<?php

declare(strict_types=1);

namespace App\Localizing\Command;

use App\Localizing\ServiceInterface\LocaleCodeNameConverterServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'localizing:locale-name', description: 'Resolve a locale code to a localized display name.')]
/**
 * Resolves locale codes into display names for diagnostic and operator workflows.
 */
final class LocaleNameCommand extends Command
{
    public function __construct(private readonly LocaleCodeNameConverterServiceInterface $converter)
    {
        parent::__construct();
    }

    /**
     * Declares the locale code and display-locale arguments accepted by the command.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('code', InputArgument::REQUIRED, 'Locale code, for example en or uk-UA')
            ->addArgument('display-locale', InputArgument::OPTIONAL, 'Locale used to display the name', 'en');
    }

    /**
     * Converts the requested locale code and prints the localized display name.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $code = $this->stringArgument($input, 'code');
        $displayLocale = $this->stringArgument($input, 'display-locale');
        $io->writeln($this->converter->convertCodeToName($code, $displayLocale));

        return Command::SUCCESS;
    }

    private function stringArgument(InputInterface $input, string $name): string
    {
        $value = $input->getArgument($name);
        if (!is_scalar($value)) {
            throw new \InvalidArgumentException(sprintf('Argument "%s" must be scalar.', $name));
        }

        return (string) $value;
    }
}
