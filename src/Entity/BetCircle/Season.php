<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\SeasonStatus;
use App\Repository\BetCircle\SeasonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[ORM\Table(name: 'betcircle_season')]
#[ORM\HasLifecycleCallbacks]
class Season implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $code = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20, enumType: SeasonStatus::class)]
    private SeasonStatus $status = SeasonStatus::DRAFT;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'date_immutable')]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column]
    private bool $isVisible = false;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: GameWeek::class, mappedBy: 'season', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $gameWeeks;

    public function __construct()
    {
        $this->gameWeeks = new ArrayCollection();
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
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

    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = trim($name);

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = strtoupper(trim($code));

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description !== null ? trim($description) : null;

        return $this;
    }

    public function getStatus(): SeasonStatus
    {
        return $this->status;
    }


    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatus(SeasonStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getVisible(): bool
    {
        return $this->isVisible();
    }

    public function setVisible(bool $visible): self
    {
        return $this->setIsVisible($visible);
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
     * @return Collection<int, GameWeek>
     */
    public function getGameWeeks(): Collection
    {
        return $this->gameWeeks;
    }

    public function addGameWeek(GameWeek $gameWeek): self
    {
        if (!$this->gameWeeks->contains($gameWeek)) {
            $this->gameWeeks->add($gameWeek);
            $gameWeek->setSeason($this);
        }

        return $this;
    }

    public function removeGameWeek(GameWeek $gameWeek): self
    {
        $this->gameWeeks->removeElement($gameWeek);

        return $this;
    }
}
