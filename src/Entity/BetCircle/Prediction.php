<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\PredictionOutcome;
use App\Repository\BetCircle\PredictionRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: PredictionRepository::class)]
#[ORM\Table(name: 'betcircle_prediction')]
#[ORM\UniqueConstraint(name: 'uniq_betcircle_prediction_fixture_customer', columns: ['fixture_id', 'customer_id'])]
#[ORM\HasLifecycleCallbacks]
class Prediction implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Fixture::class)]
    #[ORM\JoinColumn(name: 'fixture_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Fixture $fixture = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class)]
    #[ORM\JoinColumn(name: 'game_week_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?GameWeek $gameWeek = null;

    #[ORM\ManyToOne(targetEntity: WeekEntry::class)]
    #[ORM\JoinColumn(name: 'week_entry_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?WeekEntry $weekEntry = null;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\Column]
    private int $predictedHomeScore = 0;

    #[ORM\Column]
    private int $predictedAwayScore = 0;

    #[ORM\Column(length: 20, enumType: PredictionOutcome::class)]
    private PredictionOutcome $predictedOutcome;

    #[ORM\Column]
    private int $awardedPoints = 0;

    #[ORM\Column]
    private bool $isScored = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $submittedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lockedAt = null;

    public function __construct()
    {
        $now = new \DateTimeImmutable();

        $this->predictedOutcome = PredictionOutcome::DRAW;
        $this->submittedAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new \DateTimeImmutable();

        $this->submittedAt = $now;
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

    public function getFixture(): ?Fixture
    {
        return $this->fixture;
    }

    public function setFixture(?Fixture $fixture): self
    {
        $this->fixture = $fixture;

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

    public function getWeekEntry(): ?WeekEntry
    {
        return $this->weekEntry;
    }

    public function setWeekEntry(?WeekEntry $weekEntry): self
    {
        $this->weekEntry = $weekEntry;

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

    public function getPredictedHomeScore(): int
    {
        return $this->predictedHomeScore;
    }

    public function setPredictedHomeScore(int $predictedHomeScore): self
    {
        $this->predictedHomeScore = $predictedHomeScore;

        return $this;
    }

    public function getPredictedAwayScore(): int
    {
        return $this->predictedAwayScore;
    }

    public function setPredictedAwayScore(int $predictedAwayScore): self
    {
        $this->predictedAwayScore = $predictedAwayScore;

        return $this;
    }

    public function getPredictedOutcome(): PredictionOutcome
    {
        return $this->predictedOutcome;
    }

    public function getPredictedOutcomeValue(): string
    {
        return $this->predictedOutcome->value;
    }

    public function setPredictedOutcome(PredictionOutcome $predictedOutcome): self
    {
        $this->predictedOutcome = $predictedOutcome;

        return $this;
    }

    public function getAwardedPoints(): int
    {
        return $this->awardedPoints;
    }

    public function setAwardedPoints(int $awardedPoints): self
    {
        $this->awardedPoints = $awardedPoints;

        return $this;
    }

    public function isScored(): bool
    {
        return $this->isScored;
    }

    public function setIsScored(bool $isScored): self
    {
        $this->isScored = $isScored;

        return $this;
    }

    public function getSubmittedAt(): \DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getLockedAt(): ?\DateTimeImmutable
    {
        return $this->lockedAt;
    }

    public function setLockedAt(?\DateTimeImmutable $lockedAt): self
    {
        $this->lockedAt = $lockedAt;

        return $this;
    }
}
