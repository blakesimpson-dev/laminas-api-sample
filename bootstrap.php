<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(
    __DIR__,
    ['.env', '.env.local'],
    false,
    'UTF-8',
);

$dotenv->load();
$dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD'])->notEmpty();
