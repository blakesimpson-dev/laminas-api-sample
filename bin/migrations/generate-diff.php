<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Generator\Exception\NoChangesDetected;

try {
    require dirname(__DIR__, 2) . '/bootstrap.php';
    generate_diff();
} catch (NoChangesDetected) {
    fwrite(STDOUT, 'No changes detected.\n');
    exit(0);
} catch (Throwable $e) {
    $detail = implode(' - ', [$e::class, $e->getMessage()]);
    fwrite(STDERR, "Generate diff failed: \n{$detail}\n");
    exit(1);
}

/**
 * @throws NoChangesDetected
 * @throws RuntimeException
 */
function generate_diff(): void
{
    /** @var DependencyFactory $dependencyFactory */
    $dependencyFactory = require __DIR__ . '/config/dependency-factory.php';

    $migrationClassNamespace = array_key_first(
        $dependencyFactory->getConfiguration()->getMigrationDirectories(),
    );

    if (!$migrationClassNamespace) {
        throw new RuntimeException('Migration namespace not found.');
    }

    $migrationClassName = $dependencyFactory
        ->getClassNameGenerator()
        ->generateClassName($migrationClassNamespace);

    $resultingPath = $dependencyFactory->getDiffGenerator()->generate(
        $migrationClassName,
        null,
    );

    echo "Generated migration: {$resultingPath}\n";
}
