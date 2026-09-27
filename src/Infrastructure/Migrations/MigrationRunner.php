<?php

declare(strict_types=1);

namespace LaminasApiSample\Infrastructure\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Exception\NoMigrationsToExecute;
use Doctrine\Migrations\Metadata\MigrationPlanList;
use Doctrine\Migrations\MigratorConfiguration;

final readonly class MigrationRunner
{
    public function __construct(
        private DependencyFactory $dependencyFactory,
    ) {}

    /** @throws NoMigrationsToExecute */
    public function run(): void
    {
        $config = new MigratorConfiguration()->setAllOrNothing(
            $this->dependencyFactory->getConfiguration()->isAllOrNothing(),
        );

        $this->dependencyFactory->getMigrator()->migrate(
            $this->plan(),
            $config,
        );
    }

    /** @throws NoMigrationsToExecute */
    private function plan(): MigrationPlanList
    {
        $this->dependencyFactory->getMetadataStorage()->ensureInitialized();

        $latest = $this->dependencyFactory
            ->getVersionAliasResolver()
            ->resolveVersionAlias('latest');

        $plan = $this->dependencyFactory
            ->getMigrationPlanCalculator()
            ->getPlanUntilVersion($latest);

        if (count($plan) === 0) {
            throw NoMigrationsToExecute::new();
        }

        return $plan;
    }
}
