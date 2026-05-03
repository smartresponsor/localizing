<?php

declare(strict_types=1);

namespace App\Localizing\Controller;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use App\Localizing\ServiceInterface\Template\LocaleTemplateContextProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleCatalogController extends AbstractController
{
    public function __construct(
        private readonly LocaleCatalogScannerInterface $catalogScanner,
        private readonly LocaleTemplateContextProviderInterface $localeTemplateContextProvider,
    ) {
    }

    #[Route('/localizing/catalogs', name: 'localizing_catalog_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $messages = $this->catalogScanner->scan();

        return $this->json([
            'component' => 'Localizing',
            'count' => count($messages),
        ]);
    }

    #[Route('/localizing/template-context/{localeCode}', name: 'localizing_template_context', methods: ['GET'])]
    public function templateContext(string $localeCode, Request $request): JsonResponse
    {
        $domains = array_values(array_filter(array_map(
            static fn (string $domain): string => trim($domain),
            explode(',', (string) $request->query->get('domains', '')),
        )));

        return $this->json($this->localeTemplateContextProvider->provide($localeCode, $domains)->toArray());
    }
}
