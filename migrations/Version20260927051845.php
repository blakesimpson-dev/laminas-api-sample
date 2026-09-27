<?php

declare(strict_types=1);

namespace LaminasApiSample\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration Version20260927051845
 * This runner was auto-generated with Doctrine
 */
final class Version20260927051845 extends AbstractMigration
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
            'ALTER TABLE item_filter ADD profile_id UUID DEFAULT NULL',
        );
        $this->addSql(
            'UPDATE item_filter SET profile_id = (SELECT uuid FROM profile ORDER BY created_at, uuid LIMIT 1)',
        );
        $this->addSql('ALTER TABLE item_filter ALTER profile_id SET NOT NULL');
        $this->addSql(
            'ALTER TABLE item_filter ADD CONSTRAINT FK_F93D2AD2CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (uuid) NOT DEFERRABLE',
        );
        $this->addSql(
            'CREATE INDEX IDX_F93D2AD2CCFA12B8 ON item_filter (profile_id)',
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
            'ALTER TABLE item_filter DROP CONSTRAINT FK_F93D2AD2CCFA12B8',
        );
        $this->addSql('DROP INDEX IDX_F93D2AD2CCFA12B8');
        $this->addSql('ALTER TABLE item_filter DROP profile_id');
    }
}
