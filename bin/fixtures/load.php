<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\DoctrineEntityManagerFactory as DoctrineEMF;
use Psr\Container\ContainerExceptionInterface;

try {
    load_fixtures(build_loader());
} catch (Throwable $e) {
    fwrite(STDERR, "Loading fixtures failed: {$e->getMessage()}\n");
    exit(1);
}

function build_loader(): Loader
{
    $loader = new Loader();
    $loader->loadFromDirectory(dirname(__DIR__, 2) . "/test/Fixtures");

    return $loader;
}

/** @throws ContainerExceptionInterface */
function load_fixtures(Loader $loader): void
{
    /** @var ServiceManager $serviceManager */
    $serviceManager = require dirname(__DIR__, 2) .
        "/config/service-manager.php";

    /** @var EntityManager $entityManager */
    $entityManager = $serviceManager->get(DoctrineEMF::SERVICE_NAME);

    // ! Purge is destructive - be careful!
    $executor = new ORMExecutor($entityManager, new ORMPurger());
    $executor->execute($loader->getFixtures());
    echo "Loading fixtures completed.\n";
}
