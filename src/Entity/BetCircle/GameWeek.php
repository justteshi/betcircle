<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\GameWeekStatus;
use App\Repository\BetCircle\GameWeekRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


#[ORM\Entity(repositoryClass: GameWeekRepository::class)]
#[ORM\Table(name: 'betcircle_game_week')]
#[ORM\HasLifecycleCallbacks]
class GameWeek implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Season::class, inversedBy: 'gameWeeks')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Season $season;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20, enumType: GameWeekStatus::class)]
    private GameWeekStatus $status = GameWeekStatus::DRAFT;

    #[ORM\Column]
    private int $entryCostTokens = 0;

    #[ORM\Column]
    private int $weeklyPoolTokens = 0;

    #[ORM\Column]
    private int $seasonalPoolContributionTokens = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $joinDeadlineAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $predictionLockAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $firstFixtureStartsAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $lastFixtureEndsAt = null;

    #[ORM\Column]
    private bool $visible = false;

    #[ORM\Column]
    private bool $finalized = false;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $finalizedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: Fixture::class, mappedBy: 'gameWeek', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['displayOrder' => 'ASC', 'kickoffAt' => 'ASC', 'id' => 'ASC'])]
    private Collection $fixtures;

    public function __construct()
    {
        $this->fixtures = new ArrayCollection();

        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->joinDeadlineAt = $now;
        $this->predictionLockAt = $now;
        $this->firstFixtureStartsAt = $now;
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

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function getStatus(): GameWeekStatus
    {
        return $this->status;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatus(GameWeekStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEntryCostTokens(): int
    {
        return $this->entryCostTokens;
    }

    public function setEntryCostTokens(int $entryCostTokens): self
    {
        $this->entryCostTokens = $entryCostTokens;

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

    public function getSeasonalPoolContributionTokens(): int
    {
        return $this->seasonalPoolContributionTokens;
    }

    public function setSeasonalPoolContributionTokens(int $seasonalPoolContributionTokens): self
    {
        $this->seasonalPoolContributionTokens = $seasonalPoolContributionTokens;

        return $this;
    }

    public function getJoinDeadlineAt(): \DateTimeImmutable
    {
        return $this->joinDeadlineAt;
    }

    public function setJoinDeadlineAt(\DateTimeImmutable $joinDeadlineAt): self
    {
        $this->joinDeadlineAt = $joinDeadlineAt;

        return $this;
    }

    public function getPredictionLockAt(): \DateTimeImmutable
    {
        return $this->predictionLockAt;
    }

    public function setPredictionLockAt(\DateTimeImmutable $predictionLockAt): self
    {
        $this->predictionLockAt = $predictionLockAt;

        return $this;
    }

    public function getFirstFixtureStartsAt(): \DateTimeImmutable
    {
        return $this->firstFixtureStartsAt;
    }

    public function setFirstFixtureStartsAt(\DateTimeImmutable $firstFixtureStartsAt): self
    {
        $this->firstFixtureStartsAt = $firstFixtureStartsAt;

        return $this;
    }

    public function getLastFixtureEndsAt(): ?\DateTimeImmutable
    {
        return $this->lastFixtureEndsAt;
    }

    public function setLastFixtureEndsAt(?\DateTimeImmutable $lastFixtureEndsAt): self
    {
        $this->lastFixtureEndsAt = $lastFixtureEndsAt;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function isFinalized(): bool
    {
        return $this->finalized;
    }

    public function setFinalized(bool $finalized): self
    {
        $this->finalized = $finalized;

        return $this;
    }

    public function getFinalizedAt(): ?\DateTimeImmutable
    {
        return $this->finalizedAt;
    }

    public function setFinalizedAt(?\DateTimeImmutable $finalizedAt): self
    {
        $this->finalizedAt = $finalizedAt;

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

    /**
     * @return Collection<int, Fixture>
     */
    public function getFixtures(): Collection
    {
        return $this->fixtures;
    }

    public function addFixture(Fixture $fixture): self
    {
        if (!$this->fixtures->contains($fixture)) {
            $this->fixtures->add($fixture);
            $fixture->setGameWeek($this);
        }

        return $this;
    }

    public function removeFixture(Fixture $fixture): self
    {
        $this->fixtures->removeElement($fixture);

        return $this;
    }

    #[Assert\Callback]
    public function validateTokenDistribution(ExecutionContextInterface $context): void
    {
        if ($this->entryCostTokens !== $this->weeklyPoolTokens + $this->seasonalPoolContributionTokens) {
            $context->buildViolation('Weekly + Seasonal tokens must equal entry cost.')
                ->atPath('entryCostTokens')
                ->addViolation();
        }
    }
}
