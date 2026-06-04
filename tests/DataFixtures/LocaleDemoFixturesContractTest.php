<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\DataFixtures\LocaleDemoFixtures;
use App\Entity\LocaleTranslationMessageEntity;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;

final class LocaleDemoFixturesContractTest extends TestCase
{
    public function testDemoFixturesPersistIntegerPrimaryKeysForMessages(): void
    {
        $entityManager = $this->entityManager();
        $executor = new ORMExecutor($entityManager, new ORMPurger());
        $executor->execute([new LocaleDemoFixtures()], append: false);

        $messages = $entityManager->createQuery('SELECT message FROM '.LocaleTranslationMessageEntity::class.' message ORDER BY message.id ASC')->getResult();

        self::assertCount(27, $messages);

        foreach ($messages as $message) {
            self::assertIsInt($message->getId());
            self::assertGreaterThan(0, $message->getId());
            self::assertNotSame('', $message->getLocaleCode());
            self::assertNotSame('', $message->getDomainName());
            self::assertNotSame('', $message->getKeyName());
            self::assertNotSame('', $message->getMessage());
        }
    }

    private function entityManager(): EntityManager
    {
        $projectDir = dirname(__DIR__);
        $config = ORMSetup::createAttributeMetadataConfig([$projectDir.'/src/Entity'], true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());
        $config->enableNativeLazyObjects(true);
        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);

        $entityManager = new EntityManager($connection, $config);
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        return $entityManager;
    }
}
