<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260307232147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE betcircle_prediction_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE betcircle_prediction (id INT NOT NULL, fixture_id INT NOT NULL, game_week_id INT NOT NULL, week_entry_id INT NOT NULL, customer_id INT NOT NULL, predictedHomeScore INT NOT NULL, predictedAwayScore INT NOT NULL, predictedOutcome VARCHAR(20) NOT NULL, awardedPoints INT NOT NULL, isScored BOOLEAN NOT NULL, submittedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updatedAt TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, lockedAt TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FDB75129E524616D ON betcircle_prediction (fixture_id)');
        $this->addSql('CREATE INDEX IDX_FDB751295DAD4400 ON betcircle_prediction (game_week_id)');
        $this->addSql('CREATE INDEX IDX_FDB75129279FA37 ON betcircle_prediction (week_entry_id)');
        $this->addSql('CREATE INDEX IDX_FDB751299395C3F3 ON betcircle_prediction (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_betcircle_prediction_fixture_customer ON betcircle_prediction (fixture_id, customer_id)');
        $this->addSql('COMMENT ON COLUMN betcircle_prediction.submittedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prediction.updatedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN betcircle_prediction.lockedAt IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE betcircle_prediction ADD CONSTRAINT FK_FDB75129E524616D FOREIGN KEY (fixture_id) REFERENCES betcircle_fixture (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_prediction ADD CONSTRAINT FK_FDB751295DAD4400 FOREIGN KEY (game_week_id) REFERENCES betcircle_game_week (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_prediction ADD CONSTRAINT FK_FDB75129279FA37 FOREIGN KEY (week_entry_id) REFERENCES betcircle_week_entry (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE betcircle_prediction ADD CONSTRAINT FK_FDB751299395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE betcircle_prediction_id_seq CASCADE');
        $this->addSql('ALTER TABLE betcircle_prediction DROP CONSTRAINT FK_FDB75129E524616D');
        $this->addSql('ALTER TABLE betcircle_prediction DROP CONSTRAINT FK_FDB751295DAD4400');
        $this->addSql('ALTER TABLE betcircle_prediction DROP CONSTRAINT FK_FDB75129279FA37');
        $this->addSql('ALTER TABLE betcircle_prediction DROP CONSTRAINT FK_FDB751299395C3F3');
        $this->addSql('DROP TABLE betcircle_prediction');
    }
}
