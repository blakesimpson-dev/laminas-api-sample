<?php

declare(strict_types=1);

require dirname(__DIR__, 2) . "/vendor/autoload.php";

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Generator\Exception\NoChangesDetected;

try {
    generate_diff();
} catch (NoChangesDetected $e) {
    fwrite(STDOUT, "Generate diff status: {$e->getMessage()}\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "Generate diff failed: {$e->getMessage()}\n");
    exit(1);
}

/**
 * @throws NoChangesDetected
 * @throws RuntimeException
 */
function generate_diff(): string
{
    /** @var DependencyFactory $dependencyFactory */
    $dependencyFactory = require dirname(__DIR__, 2) .
        "/bin/migrations/config/dependency-factory.php";

    $migrationClassNamespace = array_key_first(
        $dependencyFactory->getConfiguration()->getMigrationDirectories(),
    );

    if (!$migrationClassNamespace) {
        throw new RuntimeException("Migration namespace not found.");
    }

    $migrationClassName = $dependencyFactory
        ->getClassNameGenerator()
        ->generateClassName($migrationClassNamespace);

    $resultingPath = $dependencyFactory
        ->getDiffGenerator()
        ->generate($migrationClassName, null);

    echo "Generated migration: {$resultingPath}\n";
    return $resultingPath;
}
