<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307234754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_prize_payout_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_prize_payout (id INT NOT NULL, season_id INT DEFAULT NULL, game_week_id INT DEFAULT NULL, customer_id INT NOT NULL, type VARCHAR(20) NOT NULL, amountTokens INT NOT NULL, status VARCHAR(20) NOT NULL, availableAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, requestedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, approvedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, paidAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_85D0E1F04EC001D1 ON betcircle_prize_payout (season_id)');
        $this->addSql('CREATE INDEX IDX_85D0E1F05DAD4400 ON betcircle_prize_payout (game_week_id)');
        $this->addSql('CREATE INDEX IDX_85D0E1F09395C3F3 ON betcircle_prize_payout (customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.availableAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.requestedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.approvedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.paidAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prize_payout.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_prize_payout ADD CONSTRAINT FK_85D0E1F04EC001D1 FOREIGN KEY (season_id) REFERENCES betcircle_season (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_prize_payout ADD CONSTRAINT FK_85D0E1F05DAD4400 FOREIGN KEY (game_week_id) REFERENCES betcircle_game_week (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_prize_payout ADD CONSTRAINT FK_85D0E1F09395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_prize_payout_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_prize_payout DROP CONSTRAINT FK_85D0E1F04EC001D1');
        $this->addSql('ALTER TABLE betcircle_prize_payout DROP CONSTRAINT FK_85D0E1F05DAD4400');
        $this->addSql('ALTER TABLE betcircle_prize_payout DROP CONSTRAINT FK_85D0E1F09395C3F3');
        $this->addSql('DROP TABLE betcircle_prize_payout');
    }
}
