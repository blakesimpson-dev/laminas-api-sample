<?php

declare(strict_types=1);

use Doctrine\DBAL\Driver\PDO\PgSQL\Driver;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver_class' => Driver::class,
                'params' => [
                    'host' => 'localhost',
                    'port' => 5432,
                    'dbname' => $_ENV['DB_NAME'],
                    'user' => $_ENV['DB_USER'],
                    'password' => $_ENV['DB_PASSWORD'],
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => AttributeDriver::class,
                'paths' => [dirname(__DIR__) . '/src'],
            ],
        ],
        'migrations' => [
            'orm_default' => [
                'table_storage' => [
                    'table_name' => 'migrations_executed',
                    'version_column_name' => 'version',
                    'version_column_length' => 255,
                    'executed_at_column_name' => 'executed_at',
                    'execution_time_column_name' => 'execution_time',
                ],
                'migrations_paths' => [
                    "LaminasApiSample\\Migrations" =>
                        dirname(__DIR__) . '/migrations',
                ],
                'all_or_nothing' => true,
                'check_database_platform' => true,
            ],
        ],
    ],
];
