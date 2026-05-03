<?php

declare(strict_types=1);

namespace App\Localizing\Service\Catalog;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogExporterInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final readonly class LocaleSymfonyCatalogExporter implements LocaleCatalogExporterInterface
{
    public function __construct(private string $exportDirectory, private Filesystem $filesystem = new Filesystem())
    {
    }

    public function export(array $messages): int
    {
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

    /** @param array<string,string> $flat */
    private function unflatten(array $flat): array
    {
        $result = [];
        foreach ($flat as $key => $value) {
            $cursor = &$result;
            foreach (explode('.', $key) as $segment) {
                if (!isset($cursor[$segment]) || !is_array($cursor[$segment])) {
                    $cursor[$segment] = [];
                }
                $cursor = &$cursor[$segment];
            }
            $cursor = $value;
            unset($cursor);
        }

        return $result;
    }
}
