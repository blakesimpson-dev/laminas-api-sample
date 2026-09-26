<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Generator\Exception\NoChangesDetected;

try {
    require dirname(__DIR__, 2) . '/bootstrap.php';
    generate_diff();
} catch (NoChangesDetected) {
    fwrite(STDOUT, "No changes detected.\n");
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

    if ($migrationClassNamespace === null) {
        throw new RuntimeException('Migration namespace not found.');
    }

    $migrationClassName = $dependencyFactory
        ->getClassNameGenerator()
        ->generateClassName($migrationClassNamespace);

    $result = $dependencyFactory->getDiffGenerator()->generate(
        $migrationClassName,
        null,
    );

    tidy_migration($result);
    format_migration($result);
    echo "Generated migration: {$result}\n";
}

/** @throws RuntimeException */
function tidy_migration(string $path): void
{
    $contents = file_get_contents($path);
    if ($contents === false) {
        throw new RuntimeException("Could not read {$path}.");
    }

    $version = basename($path, '.php');
    $new_header = <<<EOT
    /**
     * Migration {$version}
     * This runner was auto-generated with Doctrine
     */
    EOT;

    $old_header = <<<EOT
    /**
     * Auto-generated Migration: Please modify to your needs!
     */
    EOT;

    $describe_up = <<<EOT
    // this up() migration is auto-generated, please modify it to your needs
    EOT;

    $describe_down = <<<EOT
    // this down() migration is auto-generated, please modify it to your needs
    EOT;

    $get_description = <<<EOT
        public function getDescription(): string
        {
            return '';
        }
    EOT;

    $contents = str_replace(
        [
            $old_header,
            '        ' . $describe_up . "\n",
            '        ' . $describe_down . "\n",
            $get_description . "\n",
        ],
        [$new_header, '', '', ''],
        $contents,
    );

    if (file_put_contents($path, $contents) === false) {
        throw new RuntimeException("Could not write {$path}.");
    }
}

function format_migration(string $path): void
{
    $mago = dirname(__DIR__, 2) . '/vendor/bin/mago';
    if (!is_executable($mago)) {
        fwrite(STDERR, "Mago not found; {$path} left unformatted.\n");
        return;
    }

    /** @var list<string> $output */
    $output = [];
    $exitCode = 0;
    exec(
        escapeshellarg($mago) . ' format ' . escapeshellarg($path) . ' 2>&1',
        $output,
        $exitCode,
    );

    if ($exitCode !== 0) {
        fwrite(
            STDERR,
            "Mago format failed; {$path} left unformatted:\n"
            . implode("\n", $output)
            . "\n",
        );
    }
}
