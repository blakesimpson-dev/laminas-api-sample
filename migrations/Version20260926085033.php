<?php

declare(strict_types=1);

namespace LaminasApiSample\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration Version20260926085033
 * This runner was auto-generated with Doctrine
 */
final class Version20260926085033 extends AbstractMigration
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
            'ALTER TABLE item_filter ADD created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        );
        $this->addSql(
            'ALTER TABLE item_filter ADD updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL',
        );
        $this->addSql(
            'ALTER TABLE profile ADD created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        );
        $this->addSql(
            'ALTER TABLE profile ADD updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL',
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

        $this->addSql('ALTER TABLE item_filter DROP created_at');
        $this->addSql('ALTER TABLE item_filter DROP updated_at');
        $this->addSql('ALTER TABLE profile DROP created_at');
        $this->addSql('ALTER TABLE profile DROP updated_at');
    }
}
