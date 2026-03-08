<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260308000711 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_standing_snapshot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_standing_snapshot (id INT NOT NULL, season_id INT DEFAULT NULL, game_week_id INT DEFAULT NULL, customer_id INT NOT NULL, type VARCHAR(20) NOT NULL, rank INT NOT NULL, points INT NOT NULL, prizeTokens INT NOT NULL, winner BOOLEAN NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D38473614EC001D1 ON betcircle_standing_snapshot (season_id)');
        $this->addSql('CREATE INDEX IDX_D38473615DAD4400 ON betcircle_standing_snapshot (game_week_id)');
        $this->addSql('CREATE INDEX IDX_D38473619395C3F3 ON betcircle_standing_snapshot (customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_standing_snapshot.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot ADD CONSTRAINT FK_D38473614EC001D1 FOREIGN KEY (season_id) REFERENCES betcircle_season (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot ADD CONSTRAINT FK_D38473615DAD4400 FOREIGN KEY (game_week_id) REFERENCES betcircle_game_week (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot ADD CONSTRAINT FK_D38473619395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_standing_snapshot_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot DROP CONSTRAINT FK_D38473614EC001D1');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot DROP CONSTRAINT FK_D38473615DAD4400');
        $this->addSql('ALTER TABLE betcircle_standing_snapshot DROP CONSTRAINT FK_D38473619395C3F3');
        $this->addSql('DROP TABLE betcircle_standing_snapshot');
    }
}
