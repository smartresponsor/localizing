<?php

declare(strict_types=1);

namespace App\Localizing\Service\Catalog;

use App\Localizing\Dto\Catalog\LocaleCatalogMessageDto;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

final readonly class LocaleFilesystemCatalogScanner implements LocaleCatalogScannerInterface
{
    public function __construct(private string $catalogDirectory)
    {
    }

    public function scan(): array
    {
        if (!is_dir($this->catalogDirectory)) {
            return [];
        }

        $messages = [];
        $finder = new Finder();
        $finder->files()->in($this->catalogDirectory)->nameEntity('*.yaml')->nameEntity('*.yml');

        foreach ($finder as $file) {
            [$domain, $locale] = $this->parseDomainAndLocale($file->getFilename());
            $payload = Yaml::parseFile($file->getPathname());
            if (!is_array($payload)) {
                continue;
            }

            foreach ($this->flatten($payload) as $key => $message) {
                if (is_scalar($message)) {
                    $messages[] = new LocaleCatalogMessageDto($locale, $domain, $key, (string) $message, $file->getPathname());
                }
            }
        }

        return $messages;
    }

    /** @return array{0:string,1:string} */
    private function parseDomainAndLocale(string $filename): array
    {
        $basename = preg_replace('/\.ya?ml$/', '', $filename) ?? $filename;
        $parts = explode('.', $basename);
        $locale = array_pop($parts) ?: 'en';
        $domain = implode('.', $parts) ?: 'messages';

        return [$domain, $locale];
    }

    /**
     * @param array<array-key, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function flatten(array $data, string $prefix = ''): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $nameEntity = '' === $prefix ? (string) $key : $prefix.'.'.$key;
            if (is_array($value)) {
                $result += $this->flatten($value, $nameEntity);
                continue;
            }
            $result[$nameEntity] = $value;
        }

        return $result;
    }
}
