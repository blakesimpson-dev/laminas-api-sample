<?php

declare(strict_types=1);

namespace LaminasApiSample\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration Version20260926172159
 * This runner was auto-generated with Doctrine
 */
final class Version20260926172159 extends AbstractMigration
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
            'CREATE TABLE access_token (created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, id UUID NOT NULL, token_hash VARCHAR(64) NOT NULL, scopes JSON NOT NULL, expires_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, revoked_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, profile_id UUID NOT NULL, PRIMARY KEY (id))',
        );
        $this->addSql(
            'CREATE UNIQUE INDEX UNIQ_B6A2DD68B3BC57DA ON access_token (token_hash)',
        );
        $this->addSql(
            'CREATE INDEX IDX_B6A2DD68CCFA12B8 ON access_token (profile_id)',
        );
        $this->addSql(
            'ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (uuid) NOT DEFERRABLE',
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

        $this->addSql(
            'ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD68CCFA12B8',
        );
        $this->addSql('DROP TABLE access_token');
    }
}
