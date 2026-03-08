<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307230115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_wallet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE betcircle_wallet_transaction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_wallet (id INT NOT NULL, customer_id INT NOT NULL, balance INT NOT NULL, active BOOLEAN NOT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_betcircle_wallet_customer ON betcircle_wallet (customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_wallet.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_wallet.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE betcircle_wallet_transaction (id INT NOT NULL, wallet_id INT NOT NULL, customer_id INT NOT NULL, type VARCHAR(50) NOT NULL, direction VARCHAR(10) NOT NULL, amount INT NOT NULL, balanceBefore INT NOT NULL, balanceAfter INT NOT NULL, referenceType VARCHAR(100) DEFAULT NULL, referenceId VARCHAR(64) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, metadata JSON DEFAULT NULL, createdAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A83AEE0712520F3 ON betcircle_wallet_transaction (wallet_id)');
        $this->addSql('CREATE INDEX IDX_A83AEE09395C3F3 ON betcircle_wallet_transaction (customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_wallet_transaction.createdAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_wallet ADD CONSTRAINT FK_7017E6429395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_wallet_transaction ADD CONSTRAINT FK_A83AEE0712520F3 FOREIGN KEY (wallet_id) REFERENCES betcircle_wallet (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_wallet_transaction ADD CONSTRAINT FK_A83AEE09395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER visible DROP DEFAULT');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER finalized DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_wallet_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE betcircle_wallet_transaction_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_wallet DROP CONSTRAINT FK_7017E6429395C3F3');
        $this->addSql('ALTER TABLE betcircle_wallet_transaction DROP CONSTRAINT FK_A83AEE0712520F3');
        $this->addSql('ALTER TABLE betcircle_wallet_transaction DROP CONSTRAINT FK_A83AEE09395C3F3');
        $this->addSql('DROP TABLE betcircle_wallet');
        $this->addSql('DROP TABLE betcircle_wallet_transaction');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER visible SET DEFAULT false');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER finalized SET DEFAULT false');
    }
}
