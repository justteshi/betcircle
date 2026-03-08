<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307201550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_league_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_league (id INT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, country VARCHAR(100) DEFAULT NULL, isActive BOOLEAN NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32CBB745989D9B62 ON betcircle_league (slug)');
        $this->addSql('COMMENT ON COLUMN betcircle_league.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_league.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_team ADD league_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE betcircle_team DROP COLUMN IF EXISTS leaguename');
        $this->addSql('ALTER TABLE betcircle_team ADD CONSTRAINT FK_A7A5EB6C58AFC4DE FOREIGN KEY (league_id) REFERENCES betcircle_league (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A7A5EB6C58AFC4DE ON betcircle_team (league_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE betcircle_team DROP CONSTRAINT FK_A7A5EB6C58AFC4DE');
        $this->addSql('DROP SEQUENCE betcircle_league_id_seq CASCADE');
        $this->addSql('DROP TABLE betcircle_league');
        $this->addSql('DROP INDEX IDX_A7A5EB6C58AFC4DE');
        $this->addSql('ALTER TABLE betcircle_team ADD leaguename VARCHAR(150) DEFAULT NULL');
        $this->addSql('ALTER TABLE betcircle_team DROP league_id');
    }
}
