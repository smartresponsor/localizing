<?php

declare(strict_types=1);

namespace App\Service\Catalog;

use App\ServiceInterface\Catalog\LocaleCatalogExporterInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final readonly class LocaleSymfonyCatalogExporter implements LocaleCatalogExporterInterface
{
    public function __construct(private string $exportDirectory, private Filesystem $filesystem = new Filesystem())
    {
    }

    public function export(array $messages): int
    {
        /** @var array<string, array<string, array<string, string>>> $grouped */
        $grouped = [];
        foreach ($messages as $message) {
            $grouped[$message->domainName][$message->localeCode][$message->keyName] = $message->message;
        }

        $count = 0;
        foreach ($grouped as $domain => $locales) {
            foreach ($locales as $locale => $values) {
                ksort($values);
                $path = sprintf('%s/%s.%s.yaml', rtrim($this->exportDirectory, '/'), $domain, $locale);
                $this->filesystem->mkdir(dirname($path));
                file_put_contents($path, Yaml::dump($this->unflatten($values), 8, 2));
                ++$count;
            }
        }

        return $count;
    }

    /**
     * @param array<string, string> $flat
     *
     * @return array<string, mixed>
     */
    private function unflatten(array $flat): array
    {
        $result = [];
        foreach ($flat as $key => $value) {
            $this->writeNestedValue($result, explode('.', $key), $value);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>   $target
     * @param non-empty-list<string> $segments
     */
    private function writeNestedValue(array &$target, array $segments, string $value): void
    {
        $segment = array_shift($segments);

        if ([] === $segments) {
            $target[$segment] = $value;

            return;
        }

        if (!isset($target[$segment]) || !is_array($target[$segment])) {
            $target[$segment] = [];
        }

        /** @var array<string, mixed> $nested */
        $nested = &$target[$segment];
        $this->writeNestedValue($nested, $segments, $value);
    }
}
