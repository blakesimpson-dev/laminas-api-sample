<?php

declare(strict_types=1);

namespace LaminasApiSample\Infrastructure\Migrations;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Generator\Exception\NoChangesDetected;
use RuntimeException;

final readonly class MigrationDiffGenerator
{
    public function __construct(
        private DependencyFactory $dependencyFactory,
        private string $magoPath,
    ) {}

    /**
     * @throws NoChangesDetected
     * @throws RuntimeException
     */
    public function generate(): string
    {
        $namespace = array_key_first(
            $this->dependencyFactory
                ->getConfiguration()
                ->getMigrationDirectories(),
        );
        if ($namespace === null) {
            throw new RuntimeException('Migration namespace not found.');
        }

        $className = $this->dependencyFactory
            ->getClassNameGenerator()
            ->generateClassName($namespace);

        $path = $this->dependencyFactory->getDiffGenerator()->generate(
            $className,
            null,
        );

        $this->tidy($path);
        $this->format($path);

        return $path;
    }

    /** @throws RuntimeException */
    private function tidy(string $path): void
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

    private function format(string $path): void
    {
        if (!is_executable($this->magoPath)) {
            fwrite(STDERR, "Mago not found; {$path} left unformatted.\n");
            return;
        }

        /** @var list<string> $output */
        $output = [];
        $exitCode = 0;
        exec(
            escapeshellarg($this->magoPath)
            . ' format '
            . escapeshellarg($path)
            . ' 2>&1',
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
}
