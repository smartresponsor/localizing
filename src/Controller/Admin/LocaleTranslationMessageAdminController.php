<?php

declare(strict_types=1);

namespace App\Localizing\Controller\Admin;

use App\Localizing\Entity\LocaleTranslationMessageEntity;
use App\Localizing\Repository\LocaleTranslationMessageEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class LocaleTranslationMessageAdminController
{
    public function __construct(
        private LocaleTranslationMessageEntityRepository $messageRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function list(Request $request): JsonResponse
    {
        $criteria = [];
        foreach (['locale' => 'localeCode', 'domain' => 'domainName', 'key' => 'keyName'] as $param => $field) {
            $value = $request->query->get($param);
            if (is_string($value) && '' !== trim($value)) {
                $criteria[$field] = trim($value);
            }
        }

        $messages = array_map(
            static fn ($msg) => [
                'id' => $msg->getId(),
                'locale' => $msg->getLocaleCode(),
                'domain' => $msg->getDomainName(),
                'key' => $msg->getKeyName(),
                'message' => $msg->getMessage(),
            ],
            $this->messageRepository->findBy($criteria, ['localeCode' => 'ASC', 'domainName' => 'ASC']),
        );

        return new JsonResponse(['messages' => $messages, 'total' => count($messages)]);
    }

    public function upsert(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }
        $localeValue = $data['locale'] ?? null;
        $domainValue = $data['domain'] ?? null;
        $keyValue = $data['key'] ?? null;
        $messageValue = $data['message'] ?? null;
        $locale = is_scalar($localeValue) ? trim((string) $localeValue) : '';
        $domain = is_scalar($domainValue) ? trim((string) $domainValue) : '';
        $key = is_scalar($keyValue) ? trim((string) $keyValue) : '';
        $message = is_scalar($messageValue) ? (string) $messageValue : '';

        if ('' === $locale || '' === $domain || '' === $key) {
            return new JsonResponse(['error' => 'locale, domain and key are required'], 400);
        }

        $existing = $this->messageRepository->findOneBy([
            'localeCode' => $locale,
            'domainName' => $domain,
            'keyName' => $key,
        ]);

        if (null !== $existing) {
            $existing->updateMessage($message);
            $this->entityManager->flush();

            return new JsonResponse([
                'id' => $existing->getId(),
                'locale' => $existing->getLocaleCode(),
                'domain' => $existing->getDomainName(),
                'key' => $existing->getKeyName(),
                'message' => $existing->getMessage(),
                'created' => false,
            ]);
        }

        $entity = new LocaleTranslationMessageEntity($locale, $domain, $key, $message);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $entity->getId(),
            'locale' => $entity->getLocaleCode(),
            'domain' => $entity->getDomainName(),
            'key' => $entity->getKeyName(),
            'message' => $entity->getMessage(),
            'created' => true,
        ], 201);
    }
}
