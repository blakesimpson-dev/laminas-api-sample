<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Generator\Exception\NoChangesDetected;
use LaminasApiSample\Infrastructure\Migrations\MigrationDiffGenerator;

(static function (): void {
    try {
        require dirname(__DIR__, 2) . '/bootstrap.php';

        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = require __DIR__ . '/config/dependency-factory.php';

        $generator = new MigrationDiffGenerator(
            $dependencyFactory,
            dirname(__DIR__, 2) . '/vendor/bin/mago',
        );
        echo 'Generated migration: ' . $generator->generate() . "\n";
    } catch (NoChangesDetected) {
        echo "No changes detected.\n";
    } catch (Throwable $exception) {
        $detail = implode(' - ', [$exception::class, $exception->getMessage()]);
        fwrite(STDERR, "Generate diff failed:\n{$detail}\n");
        exit(1);
    }
})();
