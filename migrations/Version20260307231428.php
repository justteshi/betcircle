<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307231428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_week_entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_week_entry (id INT NOT NULL, game_week_id INT NOT NULL, wallet_transaction_id INT DEFAULT NULL, customer_id INT NOT NULL, status VARCHAR(20) NOT NULL, entryCostTokens INT NOT NULL, weeklyContributionTokens INT NOT NULL, seasonalContributionTokens INT NOT NULL, joinedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2B046A125DAD4400 ON betcircle_week_entry (game_week_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B046A12924C1837 ON betcircle_week_entry (wallet_transaction_id)');
        $this->addSql('CREATE INDEX IDX_2B046A129395C3F3 ON betcircle_week_entry (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_betcircle_week_entry_game_week_customer ON betcircle_week_entry (game_week_id, customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_week_entry.joinedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_week_entry.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_week_entry.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_week_entry ADD CONSTRAINT FK_2B046A125DAD4400 FOREIGN KEY (game_week_id) REFERENCES betcircle_game_week (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_week_entry ADD CONSTRAINT FK_2B046A12924C1837 FOREIGN KEY (wallet_transaction_id) REFERENCES betcircle_wallet_transaction (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_week_entry ADD CONSTRAINT FK_2B046A129395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_week_entry_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_week_entry DROP CONSTRAINT FK_2B046A125DAD4400');
        $this->addSql('ALTER TABLE betcircle_week_entry DROP CONSTRAINT FK_2B046A12924C1837');
        $this->addSql('ALTER TABLE betcircle_week_entry DROP CONSTRAINT FK_2B046A129395C3F3');
        $this->addSql('DROP TABLE betcircle_week_entry');
    }
}
