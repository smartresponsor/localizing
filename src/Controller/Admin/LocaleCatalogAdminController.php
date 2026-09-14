<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogExporterServiceInterface;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Provides administrative catalog inspection and export operations for localization maintainers.
 */
final readonly class LocaleCatalogAdminController
{
    public function __construct(
        private LocaleCatalogScannerServiceInterface $scanner,
        private LocaleCatalogExporterServiceInterface $exporter,
    ) {
    }

    /**
     * Returns the currently discovered catalog messages with their source metadata.
     */
    public function scan(): JsonResponse
    {
        $messages = $this->scanner->scan();

        return new JsonResponse([
            'total' => count($messages),
            'messages' => array_map(
                static fn ($msg) => [
                    'locale' => $msg->localeCode,
                    'domain' => $msg->domainName,
                    'key' => $msg->keyName,
                    'message' => $msg->message,
                    'source' => $msg->sourcePath,
                ],
                $messages,
            ),
        ]);
    }

    /**
     * Exports the currently discovered messages and reports scan and file counts.
     */
    public function export(): JsonResponse
    {
        $messages = $this->scanner->scan();
        $exported = $this->exporter->export($messages);

        return new JsonResponse([
            'scanned' => count($messages),
            'exported' => $exported,
        ]);
    }
}
