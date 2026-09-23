<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSampleTest\Fixtures\ProfileFixture;

/** @var ServiceManager $serviceManager */
$serviceManager = require dirname(__DIR__) . '/config/service-manager.php';

/** @var Doctrine\ORM\EntityManager $entityManager */
$entityManager = $serviceManager->get('doctrine.entity_manager.orm_default');

$loader = new Loader();
$loader->addFixture(new ProfileFixture());

$executor = new ORMExecutor($entityManager, new ORMPurger());
$executor->execute($loader->getFixtures());
