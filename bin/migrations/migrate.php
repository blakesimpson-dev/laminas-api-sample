<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\NoMigrationsToExecute;
use Doctrine\Migrations\Metadata\MigrationPlanList;
use Doctrine\Migrations\MigratorConfiguration;

try {
    require dirname(__DIR__, 2) . '/bootstrap.php';
    migrate();
} catch (NoMigrationsToExecute) {
    fwrite(STDOUT, 'No migrations to execute.\n');
    exit(0);
} catch (Throwable $e) {
    $detail = implode(' - ', [$e::class, $e->getMessage()]);
    fwrite(STDERR, "Migration failed: \n{$detail}\n");
    exit(1);
}

/** @throws NoMigrationsToExecute */
function migrate(): void
{
    /** @var DependencyFactory $dependencyFactory */
    $dependencyFactory = require __DIR__ . '/config/dependency-factory.php';

    $plan = build_plan($dependencyFactory);
    $config = new MigratorConfiguration()->setAllOrNothing(
        $dependencyFactory->getConfiguration()->isAllOrNothing(),
    );

    $dependencyFactory->getMigrator()->migrate($plan, $config);
    echo "Migration completed.\n";
}

/** @throws NoMigrationsToExecute */
function build_plan(DependencyFactory $dependencyFactory): MigrationPlanList
{
    $dependencyFactory->getMetadataStorage()->ensureInitialized();

    $latestVersion = $dependencyFactory
        ->getVersionAliasResolver()
        ->resolveVersionAlias('latest');

    $plan = $dependencyFactory
        ->getMigrationPlanCalculator()
        ->getPlanUntilVersion($latestVersion);
    if (!count($plan)) {
        throw NoMigrationsToExecute::new();
    }

    return $plan;
}
