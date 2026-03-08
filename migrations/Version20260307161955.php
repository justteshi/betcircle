<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307161955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_fixture_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betcircle_game_week_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betcircle_season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betcircle_team_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_fixture (id INT NOT NULL, displayOrder INT DEFAULT NULL, kickoffAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(20) NOT NULL, homeScore INT DEFAULT NULL, awayScore INT DEFAULT NULL, resultEnteredAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notes TEXT DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, gameWeek_id INT NOT NULL, homeTeam_id INT NOT NULL, awayTeam_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_103312D383D448F8 ON betcircle_fixture (gameWeek_id)');
        $this->addSql('CREATE INDEX IDX_103312D3EFE66F0C ON betcircle_fixture (homeTeam_id)');
        $this->addSql('CREATE INDEX IDX_103312D36DF247E5 ON betcircle_fixture (awayTeam_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_fixture.kickoffAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_fixture.resultEnteredAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_fixture.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_fixture.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE betcircle_game_week (id INT NOT NULL, season_id INT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(20) NOT NULL, entryCostTokens INT NOT NULL, weeklyPoolTokens INT NOT NULL, seasonalPoolContributionTokens INT NOT NULL, joinDeadlineAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, predictionLockAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, firstFixtureStartsAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, lastFixtureEndsAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, isVisible BOOLEAN NOT NULL, isFinalized BOOLEAN NOT NULL, finalizedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5FC2685C4EC001D1 ON betcircle_game_week (season_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.joinDeadlineAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.predictionLockAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.firstFixtureStartsAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.lastFixtureEndsAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.finalizedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_game_week.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE betcircle_season (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, startDate DATE NOT NULL, endDate DATE NOT NULL, isVisible BOOLEAN NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FC9B2FF477153098 ON betcircle_season (code)');
        $this->addSql('COMMENT ON COLUMN betcircle_season.startDate IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_season.endDate IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_season.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_season.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE betcircle_team (id INT NOT NULL, name VARCHAR(255) NOT NULL, shortName VARCHAR(100) DEFAULT NULL, slug VARCHAR(255) NOT NULL, country VARCHAR(100) DEFAULT NULL, leagueName VARCHAR(150) DEFAULT NULL, logoPath VARCHAR(255) DEFAULT NULL, isActive BOOLEAN NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A7A5EB6C989D9B62 ON betcircle_team (slug)');
        $this->addSql('COMMENT ON COLUMN betcircle_team.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_team.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_fixture ADD CONSTRAINT FK_103312D383D448F8 FOREIGN KEY (gameWeek_id) REFERENCES betcircle_game_week (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_fixture ADD CONSTRAINT FK_103312D3EFE66F0C FOREIGN KEY (homeTeam_id) REFERENCES betcircle_team (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_fixture ADD CONSTRAINT FK_103312D36DF247E5 FOREIGN KEY (awayTeam_id) REFERENCES betcircle_team (id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_game_week ADD CONSTRAINT FK_5FC2685C4EC001D1 FOREIGN KEY (season_id) REFERENCES betcircle_season (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_fixture_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betcircle_game_week_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betcircle_season_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betcircle_team_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_fixture DROP CONSTRAINT FK_103312D383D448F8');
        $this->addSql('ALTER TABLE betcircle_fixture DROP CONSTRAINT FK_103312D3EFE66F0C');
        $this->addSql('ALTER TABLE betcircle_fixture DROP CONSTRAINT FK_103312D36DF247E5');
        $this->addSql('ALTER TABLE betcircle_game_week DROP CONSTRAINT FK_5FC2685C4EC001D1');
        $this->addSql('DROP TABLE betcircle_fixture');
        $this->addSql('DROP TABLE betcircle_game_week');
        $this->addSql('DROP TABLE betcircle_season');
        $this->addSql('DROP TABLE betcircle_team');
    }
}
