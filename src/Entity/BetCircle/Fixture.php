<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\FixtureStatus;
use App\Repository\BetCircle\FixtureRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: FixtureRepository::class)]
#[ORM\Table(name: 'betcircle_fixture')]
#[ORM\HasLifecycleCallbacks]
class Fixture implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class, inversedBy: 'fixtures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?GameWeek $gameWeek = null;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'homeFixtures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Team $homeTeam = null;


    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'awayFixtures')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'RESTRICT')]
    private ?Team $awayTeam = null;


    #[ORM\Column(nullable: true)]
    private ?int $displayOrder = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $kickoffAt;

    #[ORM\Column(length: 20, enumType: FixtureStatus::class)]
    private FixtureStatus $status = FixtureStatus::SCHEDULED;

    #[ORM\Column(nullable: true)]
    private ?int $homeScore = null;

    #[ORM\Column(nullable: true)]
    private ?int $awayScore = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $resultEnteredAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->kickoffAt = $now;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGameWeek(): ?GameWeek
    {
        return $this->gameWeek;
    }

    public function setGameWeek(GameWeek $gameWeek): self
    {
        $this->gameWeek = $gameWeek;

        return $this;
    }

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(Team $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): ?Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(Team $awayTeam): self
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getKickoffAt(): \DateTimeImmutable
    {
        return $this->kickoffAt;
    }

    public function setKickoffAt(\DateTimeImmutable $kickoffAt): self
    {
        $this->kickoffAt = $kickoffAt;

        return $this;
    }

    public function getStatus(): FixtureStatus
    {
        return $this->status;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatus(FixtureStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getHomeScore(): ?int
    {
        return $this->homeScore;
    }

    public function setHomeScore(?int $homeScore): self
    {
        $this->homeScore = $homeScore;

        return $this;
    }

    public function getAwayScore(): ?int
    {
        return $this->awayScore;
    }

    public function setAwayScore(?int $awayScore): self
    {
        $this->awayScore = $awayScore;

        return $this;
    }

    public function getResultEnteredAt(): ?\DateTimeImmutable
    {
        return $this->resultEnteredAt;
    }

    public function setResultEnteredAt(?\DateTimeImmutable $resultEnteredAt): self
    {
        $this->resultEnteredAt = $resultEnteredAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes !== null ? trim($notes) : null;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function hasResult(): bool
    {
        return $this->homeScore !== null && $this->awayScore !== null;
    }

    public function isFinished(): bool
    {
        return $this->status === FixtureStatus::FINISHED;
    }
}
