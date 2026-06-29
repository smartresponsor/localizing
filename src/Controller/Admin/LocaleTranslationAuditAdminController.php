<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleTranslationAuditFindingEntity;
use App\Localizing\Repository\LocaleTranslationAuditFindingEntityRepository;
use App\Localizing\ServiceInterface\Catalog\LocaleCatalogScannerInterface;
use App\Localizing\ServiceInterface\Quality\LocaleCatalogAuditorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleTranslationAuditAdminController
{
    public function __construct(
        private LocaleTranslationAuditFindingEntityRepository $findingRepository,
        private LocaleCatalogScannerInterface $scanner,
        private LocaleCatalogAuditorInterface $auditor,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function findings(Request $request): JsonResponse
    {
        $criteria = [];
        $severity = $request->query->get('severity');
        if (is_string($severity) && '' !== trim($severity)) {
            $criteria['severity'] = trim($severity);
        }

        $domain = $request->query->get('domain');
        if (is_string($domain) && '' !== trim($domain)) {
            $criteria['domainName'] = trim($domain);
        }

        $locale = $request->query->get('locale');
        if (is_string($locale) && '' !== trim($locale)) {
            $criteria['localeCode'] = trim($locale);
        }

        $findings = array_map(
            static fn ($finding) => [
                'id' => $finding->getId(),
                'severity' => $finding->getSeverity(),
                'code' => $finding->getCode(),
                'domain' => $finding->getDomainName(),
                'key' => $finding->getKeyName(),
                'locale' => $finding->getLocaleCode(),
                'message' => $finding->getMessage(),
            ],
            $this->findingRepository->findBy($criteria, ['severity' => 'ASC', 'domainName' => 'ASC']),
        );

        return new JsonResponse(['findings' => $findings, 'total' => count($findings)]);
    }

    public function run(): JsonResponse
    {
        $messages = $this->scanner->scan();
        $results = $this->auditor->audit($messages);

        foreach ($this->findingRepository->findAll() as $old) {
            $this->entityManager->remove($old);
        }

        $entities = [];
        foreach ($results as $result) {
            $entity = new LocaleTranslationAuditFindingEntity(
                $result['severity'],
                $result['code'],
                $result['domain'],
                $result['key'],
                $result['locale'],
                $result['message'],
            );
            $this->entityManager->persist($entity);
            $entities[] = $result;
        }

        $this->entityManager->flush();

        return new JsonResponse([
            'scanned' => count($messages),
            'findings' => count($entities),
            'results' => $entities,
        ]);
    }
}
