<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\NoMigrationsToExecute;
use Doctrine\Migrations\Metadata\MigrationPlanList;
use Doctrine\Migrations\MigratorConfiguration;

try {
    migrate();
} catch (NoMigrationsToExecute $e) {
    fwrite(STDOUT, "Migration status: {$e->getMessage()}\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Migration failed: {$e->getMessage()}\n");
    exit(1);
}

/** @throws NoMigrationsToExecute */
function migrate(): void
{
    /** @var DependencyFactory $dependencyFactory */
    $dependencyFactory = require dirname(__DIR__, 2) .
        "/bin/migrations/config/dependency-factory.php";

    $plan = build_migration_plan($dependencyFactory);
    $config = new MigratorConfiguration()->setAllOrNothing(
        $dependencyFactory->getConfiguration()->isAllOrNothing(),
    );

    $dependencyFactory->getMigrator()->migrate($plan, $config);
    echo "Migration completed.\n";
}

/** @throws NoMigrationsToExecute */
function build_migration_plan(
    DependencyFactory $dependencyFactory,
): MigrationPlanList {
    $dependencyFactory->getMetadataStorage()->ensureInitialized();

    $latestVersion = $dependencyFactory
        ->getVersionAliasResolver()
        ->resolveVersionAlias("latest");

    $plan = $dependencyFactory
        ->getMigrationPlanCalculator()
        ->getPlanUntilVersion($latestVersion);
    if (!count($plan)) {
        throw NoMigrationsToExecute::new();
    }

    return $plan;
}
