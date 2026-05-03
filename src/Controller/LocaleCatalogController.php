<?php

declare(strict_types=1);

namespace App\Localizing\Controller;

use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LocaleCatalogController extends AbstractController
{
    public function __construct(private readonly LocaleCatalogScannerInterface $catalogScanner)
    {
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
}
