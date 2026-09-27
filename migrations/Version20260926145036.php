<?php

declare(strict_types=1);

namespace LaminasApiSample\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration Version20260926145036
 * This runner was auto-generated with Doctrine
 */
final class Version20260926145036 extends AbstractMigration
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

        $this->addSql('ALTER TABLE profile DROP twitch_stream_name');
        $this->addSql('ALTER TABLE profile DROP twitch_stream_image');
        $this->addSql('ALTER TABLE profile DROP twitch_stream_status');
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

        $this->addSql(
            'ALTER TABLE profile ADD twitch_stream_name VARCHAR(255) DEFAULT NULL',
        );
        $this->addSql(
            'ALTER TABLE profile ADD twitch_stream_image VARCHAR(255) DEFAULT NULL',
        );
        $this->addSql(
            'ALTER TABLE profile ADD twitch_stream_status VARCHAR(255) DEFAULT NULL',
        );
    }
}
