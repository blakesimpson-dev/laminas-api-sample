<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\MigratorConfiguration;
use Laminas\ServiceManager\ServiceManager;

/** @var ServiceManager $serviceManager */
$serviceManager = require dirname(__DIR__) . '/config/service-manager.php';

/** @var Doctrine\ORM\EntityManager $entityManager */
$entityManager = $serviceManager->get('doctrine.entity_manager.orm_default');

/**
 * @var array{
 *     doctrine: array{
 *         migrations: array{
 *             orm_default: array<string, mixed>,
 *         },
 *     },
 * } $doctrineConfig
 */
$doctrineConfig = require dirname(__DIR__) . '/config/doctrine.php';

$dependencyFactory = DependencyFactory::fromEntityManager(
    new ConfigurationArray(
        $doctrineConfig['doctrine']['migrations']['orm_default'],
    ),
    new ExistingEntityManager($entityManager),
);

$migrationClassName = $dependencyFactory
    ->getClassNameGenerator()
    ->generateClassName('LaminasApiSample\\Migrations');

$migrationPath = $dependencyFactory->getDiffGenerator()->generate(
    $migrationClassName,
    null,
);

$latestVersion = $dependencyFactory
    ->getVersionAliasResolver()
    ->resolveVersionAlias('latest');

$migrationPlan = $dependencyFactory
    ->getMigrationPlanCalculator()
    ->getPlanUntilVersion($latestVersion);

$migrationConfig = new MigratorConfiguration()->setAllOrNothing(true);

$dependencyFactory->getMetadataStorage()->ensureInitialized();

echo "Generated migration: {$migrationPath}\n";
echo "Applying migrations...\n";
$dependencyFactory->getMigrator()->migrate($migrationPlan, $migrationConfig);
echo "Done.\n";
