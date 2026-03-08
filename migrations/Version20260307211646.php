<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260307211646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename isvisible/isfinalized to visible/finalized safely';
    }

    public function up(Schema $schema): void
    {
        // 1. Add new columns as nullable first
        $this->addSql('ALTER TABLE betcircle_game_week ADD visible BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE betcircle_game_week ADD finalized BOOLEAN DEFAULT FALSE');

        // 2. Copy existing values from old columns
        $this->addSql('UPDATE betcircle_game_week SET visible = isvisible');
        $this->addSql('UPDATE betcircle_game_week SET finalized = isfinalized');

        // 3. Enforce NOT NULL
        $this->addSql('ALTER TABLE betcircle_game_week ALTER COLUMN visible SET NOT NULL');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER COLUMN finalized SET NOT NULL');

        // 4. Remove old columns
        $this->addSql('ALTER TABLE betcircle_game_week DROP isvisible');
        $this->addSql('ALTER TABLE betcircle_game_week DROP isfinalized');
    }

    public function down(Schema $schema): void
    {
        // Reverse migration

        $this->addSql('ALTER TABLE betcircle_game_week ADD isvisible BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE betcircle_game_week ADD isfinalized BOOLEAN DEFAULT FALSE');

        $this->addSql('UPDATE betcircle_game_week SET isvisible = visible');
        $this->addSql('UPDATE betcircle_game_week SET isfinalized = finalized');

        $this->addSql('ALTER TABLE betcircle_game_week ALTER COLUMN isvisible SET NOT NULL');
        $this->addSql('ALTER TABLE betcircle_game_week ALTER COLUMN isfinalized SET NOT NULL');

        $this->addSql('ALTER TABLE betcircle_game_week DROP visible');
        $this->addSql('ALTER TABLE betcircle_game_week DROP finalized');
    }
}
