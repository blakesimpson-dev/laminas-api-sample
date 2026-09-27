<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Laminas\ServiceManager\ServiceManager;
use LaminasApiSample\Infrastructure\DoctrineEntityManagerFactory as DoctrineEMF;
use LaminasApiSampleTest\Fixtures\FixtureLoader;

(static function (): void {
    try {
        require dirname(__DIR__, 2) . '/bootstrap.php';

        /** @var ServiceManager $serviceManager */
        $serviceManager = require
            dirname(__DIR__, 2) . '/config/service-manager.php';

        /** @var EntityManager $entityManager */
        $entityManager = $serviceManager->get(DoctrineEMF::SERVICE_NAME);

        new FixtureLoader($entityManager)->load(
            dirname(__DIR__, 2) . '/test/Fixtures',
        );
        echo "Loading fixtures completed.\n";
    } catch (Throwable $exception) {
        $detail = implode(' - ', [$exception::class, $exception->getMessage()]);
        fwrite(STDERR, "Loading fixtures failed:\n{$detail}\n");
        exit(1);
    }
})();
