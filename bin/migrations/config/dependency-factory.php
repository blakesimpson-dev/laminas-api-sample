<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;

/** @var ServiceManager $serviceManager */
$serviceManager = require dirname(__DIR__, 3) . '/config/service-manager.php';

/** @var EntityManager $entityManager */
$entityManager = $serviceManager->get(DoctrineEMF::SERVICE_NAME);

/**
 * @var array{
 *     doctrine: array{
 *         migrations: array{
 *             orm_default: array<string, mixed>,
 *         },
 *     },
 *  } $doctrineConfig
 */
$doctrineConfig = $serviceManager->get('config');

return DependencyFactory::fromEntityManager(
    new ConfigurationArray(
        $doctrineConfig['doctrine']['migrations']['orm_default'],
    ),
    new ExistingEntityManager($entityManager),
);
