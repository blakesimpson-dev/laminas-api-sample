<?php

declare(strict_types=1);

namespace LaminasApiSample\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration Version20260923170801
 * This runner was auto-generated with Doctrine
 */
final class Version20260923170801 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !
                $this->connection->getDatabasePlatform()
                instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform
            ,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'.",
        );

        $this->addSql(
            'CREATE TABLE profile (uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) DEFAULT NULL, twitch_name VARCHAR(255) DEFAULT NULL, twitch_stream_name VARCHAR(255) DEFAULT NULL, twitch_stream_image VARCHAR(255) DEFAULT NULL, twitch_stream_status VARCHAR(255) DEFAULT NULL, PRIMARY KEY (uuid))',
        );
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            !
                $this->connection->getDatabasePlatform()
                instanceof \Doctrine\DBAL\Platforms\PostgreSQL120Platform
            ,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\PostgreSQL120Platform'.",
        );

        $this->addSql('DROP TABLE profile');
    }
}
