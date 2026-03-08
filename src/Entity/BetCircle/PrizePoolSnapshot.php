<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Repository\BetCircle\PrizePoolSnapshotRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: PrizePoolSnapshotRepository::class)]
#[ORM\Table(name: 'betcircle_prize_pool_snapshot')]
#[ORM\HasLifecycleCallbacks]
class PrizePoolSnapshot implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Season::class)]
    #[ORM\JoinColumn(name: 'season_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Season $season = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class)]
    #[ORM\JoinColumn(name: 'game_week_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?GameWeek $gameWeek = null;

    #[ORM\Column]
    private int $weeklyPoolTokens = 0;

    #[ORM\Column]
    private int $seasonalPoolTokens = 0;

    #[ORM\Column]
    private int $entryCount = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeason(): ?Season
    {
        return $this->season;
    }

    public function setSeason(?Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getGameWeek(): ?GameWeek
    {
        return $this->gameWeek;
    }

    public function setGameWeek(?GameWeek $gameWeek): self
    {
        $this->gameWeek = $gameWeek;

        return $this;
    }

    public function getWeeklyPoolTokens(): int
    {
        return $this->weeklyPoolTokens;
    }

    public function setWeeklyPoolTokens(int $weeklyPoolTokens): self
    {
        $this->weeklyPoolTokens = $weeklyPoolTokens;

        return $this;
    }

    public function getSeasonalPoolTokens(): int
    {
        return $this->seasonalPoolTokens;
    }

    public function setSeasonalPoolTokens(int $seasonalPoolTokens): self
    {
        $this->seasonalPoolTokens = $seasonalPoolTokens;

        return $this;
    }

    public function getEntryCount(): int
    {
        return $this->entryCount;
    }

    public function setEntryCount(int $entryCount): self
    {
        $this->entryCount = $entryCount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
