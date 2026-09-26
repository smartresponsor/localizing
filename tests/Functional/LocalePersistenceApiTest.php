<?php

declare(strict_types=1);

namespace App\Localizing\Tests\Functional;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LocalePersistenceApiTest extends WebTestCase
{
    public function testAdministrativePersistenceAndRuntimeResolutionWorkflow(): void
    {
        $client = $this->createClientWithSchema();

        $this->jsonRequest($client, 'POST', '/admin/locale', [
            'code' => 'uk',
            'name' => 'Ukrainian',
            'enabled' => true,
            'priority' => 10,
        ]);
        self::assertResponseStatusCodeSame(201);

        $client->request('GET', '/admin/locale');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Ukrainian', (string) $client->getResponse()->getContent());

        $client->request('PATCH', '/admin/locale/disable/uk');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('false', (string) $client->getResponse()->getContent());

        $client->request('PATCH', '/admin/locale/enable/uk');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('true', (string) $client->getResponse()->getContent());

        $this->jsonRequest($client, 'POST', '/admin/locale', [
            'code' => 'fr',
            'enabled' => true,
            'priority' => '20',
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest($client, 'POST', '/admin/locale', [
            'code' => 'fr',
        ]);
        self::assertResponseStatusCodeSame(409);

        $this->jsonRequest($client, 'POST', '/admin/locale/translation/domain', [
            'name' => 'messages',
            'component' => 'Checkout',
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest($client, 'POST', '/admin/locale/translation/key', [
            'domain' => 'messages',
            'key' => 'checkout.title',
            'component' => 'Checkout',
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest($client, 'POST', '/admin/locale/translation/key', [
            'domain' => 'messages',
            'key' => 'checkout.subtitle',
            'component' => 'Checkout',
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest($client, 'PUT', '/admin/locale/translation/message', [
            'locale' => 'uk',
            'domain' => 'messages',
            'key' => 'checkout.title',
            'message' => 'Оформлення',
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest($client, 'PUT', '/admin/locale/translation/message', [
            'locale' => 'uk',
            'domain' => 'messages',
            'key' => 'checkout.title',
            'message' => 'Оформити замовлення',
        ]);
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('false', (string) $client->getResponse()->getContent());

        $client->request('GET', '/admin/locale/translation/message?locale=uk&domain=messages&key=checkout.title');
        self::assertResponseIsSuccessful();
        $messageList = $this->decodeResponse($client);
        $messageItems = $messageList['messages'] ?? null;
        self::assertIsArray($messageItems);
        $firstMessage = $messageItems[0] ?? null;
        self::assertIsArray($firstMessage);
        self::assertSame('Оформити замовлення', $firstMessage['message'] ?? null);

        $client->request('GET', '/admin/locale/translation/message');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/locale/translation/domain');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('messages', (string) $client->getResponse()->getContent());

        $client->request('GET', '/api/locale/translation/key?domain=messages');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('checkout.title', (string) $client->getResponse()->getContent());

        $client->request('GET', '/api/locale/translation/key');
        self::assertResponseIsSuccessful();
        $client->request('GET', '/api/locale/translation/key?domain=');
        self::assertResponseIsSuccessful();
        $client->request('GET', '/api/locale/translation/key?domain=missing');
        self::assertResponseIsSuccessful();
        $missingKeys = $this->decodeResponse($client);
        self::assertSame([], $missingKeys['keys'] ?? null);

        $client->request('GET', '/api/locale/translation/message/resolve?locale=uk&domain=messages&key=checkout.title');
        self::assertResponseIsSuccessful();
        $resolvedMessage = $this->decodeResponse($client);
        self::assertSame('Оформити замовлення', $resolvedMessage['message'] ?? null);

        $this->jsonRequest($client, 'POST', '/admin/locale/terminology', [
            'source_term' => 'cart',
            'locale' => 'uk',
            'approved_term' => 'кошик',
            'note' => 'Preferred noun',
        ]);
        self::assertResponseStatusCodeSame(201);
        $terminology = $this->decodeResponse($client);
        $terminologyId = $terminology['id'] ?? null;
        self::assertIsInt($terminologyId);

        $this->jsonRequest($client, 'PATCH', '/admin/locale/terminology/'.$terminologyId, [
            'approved_term' => 'корзина',
            'note' => null,
        ]);
        self::assertResponseIsSuccessful();

        $client->request('GET', '/api/locale/terminology?locale=uk&term=cart');
        self::assertResponseIsSuccessful();
        $terminologyList = $this->decodeResponse($client);
        $terminologyItems = $terminologyList['entries'] ?? null;
        self::assertIsArray($terminologyItems);
        $firstTerminology = $terminologyItems[0] ?? null;
        self::assertIsArray($firstTerminology);
        self::assertSame('корзина', $firstTerminology['approved_term'] ?? null);

        $client->request('GET', '/api/locale/terminology');
        self::assertResponseIsSuccessful();
        $client->request('GET', '/api/locale/terminology?locale=&term=');
        self::assertResponseIsSuccessful();

        $this->jsonRequest($client, 'POST', '/admin/locale/fallback/uk-UA', [
            'fallback_locale' => 'uk',
            'position' => '1',
        ]);
        self::assertResponseStatusCodeSame(201);
        $fallback = $this->decodeResponse($client);
        $fallbackId = $fallback['id'] ?? null;
        self::assertIsInt($fallbackId);

        $this->jsonRequest($client, 'POST', '/admin/locale/fallback/uk-UA', [
            'fallback_locale' => 'uk',
            'position' => 2,
        ]);
        self::assertResponseStatusCodeSame(409);

        $client->request('GET', '/admin/locale/fallback/uk-UA');
        self::assertResponseIsSuccessful();
        self::assertStringContainsString('fallback_locale', (string) $client->getResponse()->getContent());

        $client->request('DELETE', '/admin/locale/fallback/delete/'.$fallbackId);
        self::assertResponseStatusCodeSame(204);

        $client->request('POST', '/admin/locale/audit/run');
        self::assertResponseIsSuccessful();

        $client->request('GET', '/admin/locale/audit/finding');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/admin/locale/catalog/scan');
        self::assertResponseIsSuccessful();

        $client->request('POST', '/admin/locale/catalog/export');
        self::assertResponseIsSuccessful();
    }

    public function testAdministrativeValidationAndNotFoundResponses(): void
    {
        $client = $this->createClientWithSchema();

        $this->jsonRequest($client, 'POST', '/admin/locale', []);
        self::assertResponseStatusCodeSame(400);

        $client->request('PATCH', '/admin/locale/enable/missing');
        self::assertResponseStatusCodeSame(404);

        $client->request('PATCH', '/admin/locale/disable/missing');
        self::assertResponseStatusCodeSame(404);

        $this->jsonRequest($client, 'POST', '/admin/locale/fallback/en', []);
        self::assertResponseStatusCodeSame(400);

        $client->request('DELETE', '/admin/locale/fallback/delete/999');
        self::assertResponseStatusCodeSame(404);

        $client->request('GET', '/api/locale/translation/message/resolve');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/api/locale/translation/message/resolve?locale=fr&domain=&key=missing.key');
        self::assertResponseStatusCodeSame(400);
        $client->request('GET', '/api/locale/translation/message/resolve?locale=fr&domain=messages&key=');
        self::assertResponseStatusCodeSame(400);

        $this->jsonRequest($client, 'PUT', '/admin/locale/translation/message', []);
        self::assertResponseStatusCodeSame(400);

        $client->request('PUT', '/admin/locale/translation/message', server: ['CONTENT_TYPE' => 'application/json'], content: 'not-json');
        self::assertResponseStatusCodeSame(400);

        $client->request('GET', '/api/locale/translation/message/resolve?locale=fr&domain=messages&key=missing.key');
        self::assertResponseStatusCodeSame(404);
    }

    private function createClientWithSchema(): KernelBrowser
    {
        $client = self::createClient();
        $client->disableReboot();
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        self::assertInstanceOf(EntityManagerInterface::class, $entityManager);

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);

        return $client;
    }

    /** @param array<string, mixed> $payload */
    private function jsonRequest(KernelBrowser $client, string $method, string $uri, array $payload): void
    {
        $client->request($method, $uri, server: ['CONTENT_TYPE' => 'application/json'], content: json_encode($payload, JSON_THROW_ON_ERROR));
    }

    /** @return array<mixed> */
    private function decodeResponse(KernelBrowser $client): array
    {
        $payload = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertIsArray($payload);

        return $payload;
    }
}
