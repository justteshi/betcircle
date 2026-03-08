<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\StandingSnapshotType;
use App\Repository\BetCircle\StandingSnapshotRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: StandingSnapshotRepository::class)]
#[ORM\Table(name: 'betcircle_standing_snapshot')]
#[ORM\HasLifecycleCallbacks]
class StandingSnapshot implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, enumType: StandingSnapshotType::class)]
    private StandingSnapshotType $type;

    #[ORM\ManyToOne(targetEntity: Season::class)]
    #[ORM\JoinColumn(name: 'season_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Season $season = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class)]
    #[ORM\JoinColumn(name: 'game_week_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?GameWeek $gameWeek = null;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\Column]
    private int $rank = 0;

    #[ORM\Column]
    private int $points = 0;

    #[ORM\Column]
    private int $prizeTokens = 0;

    #[ORM\Column]
    private bool $winner = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->type = StandingSnapshotType::WEEKLY;
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

    public function getType(): StandingSnapshotType
    {
        return $this->type;
    }

    public function getTypeValue(): string
    {
        return $this->type->value;
    }

    public function setType(StandingSnapshotType $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function setRank(int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getPrizeTokens(): int
    {
        return $this->prizeTokens;
    }

    public function setPrizeTokens(int $prizeTokens): self
    {
        $this->prizeTokens = $prizeTokens;

        return $this;
    }

    public function isWinner(): bool
    {
        return $this->winner;
    }

    public function setWinner(bool $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
