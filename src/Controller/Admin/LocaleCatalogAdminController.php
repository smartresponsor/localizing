<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogExporterInterface;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class LocaleCatalogAdminController
{
    public function __construct(
        private LocaleCatalogScannerInterface $scanner,
        private LocaleCatalogExporterInterface $exporter,
    ) {
    }

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
