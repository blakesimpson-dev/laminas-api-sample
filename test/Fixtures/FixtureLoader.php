<?php

declare(strict_types=1);

namespace LaminasApiSampleTest\Fixtures;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;

final readonly class FixtureLoader
{
    public function __construct(
        private EntityManager $entityManager,
    ) {}

    public function load(string $directory): void
    {
        $loader = new Loader();
        $loader->loadFromDirectory($directory);

        new ORMExecutor($this->entityManager, new ORMPurger())->execute(
            $loader->getFixtures(),
        );
    }
}
