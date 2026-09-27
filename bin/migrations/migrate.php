<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\NoMigrationsToExecute;
use LaminasApiSample\Infrastructure\Migrations\MigrationRunner;

(static function (): void {
    try {
        require dirname(__DIR__, 2) . '/bootstrap.php';

        /** @var DependencyFactory $dependencyFactory */
        $dependencyFactory = require __DIR__ . '/config/dependency-factory.php';

        new MigrationRunner($dependencyFactory)->run();
        echo "Migration completed.\n";
    } catch (NoMigrationsToExecute) {
        echo "No migrations to execute.\n";
    } catch (Throwable $exception) {
        $detail = implode(' - ', [$exception::class, $exception->getMessage()]);
        fwrite(STDERR, "Migration failed:\n{$detail}\n");
        exit(1);
    }
})();
