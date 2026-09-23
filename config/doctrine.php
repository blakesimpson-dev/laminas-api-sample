<?php

declare(strict_types=1);

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(
    __DIR__,
    ['.env', '.env.local'],
    false,
    'UTF-8',
);
$dotenv->load();

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver' => 'pdo_pgsql',
                'params' => [
                    'host' => 'localhost',
                    'port' => 5432,
                    'dbname' => 'REDACTED',
                    'user' => 'REDACTED',
                    'password' => $_ENV['DB_PASSWORD'],
                ],
            ],
        ],
        'driver' => [
            'orm_default' => [
                'class' => AttributeDriver::class,
                'paths' => [__DIR__ . '/../src'],
            ],
        ],
    ],
];
