<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Repository\BetCircle\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'betcircle_team')]
#[ORM\HasLifecycleCallbacks]
class Team implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $shortName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\ManyToOne(targetEntity: League::class, inversedBy: 'teams')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?League $league = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logoPath = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'homeTeam', targetEntity: Fixture::class)]
    private Collection $homeFixtures;

    #[ORM\OneToMany(mappedBy: 'awayTeam', targetEntity: Fixture::class)]
    private Collection $awayFixtures;

    public function __construct()
    {
        $this->homeFixtures = new ArrayCollection();
        $this->awayFixtures = new ArrayCollection();

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

    public function getShortName(): ?string
    {
        return $this->shortName;
    }

    public function setShortName(?string $shortName): self
    {
        $this->shortName = $shortName !== null ? trim($shortName) : null;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = trim($slug);

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country !== null ? trim($country) : null;

        return $this;
    }

    public function getLeague(): ?League
    {
        return $this->league;
    }

    public function getLeagueName(): string
    {
        return $this->league?->getName() ?? '—';
    }

    public function setLeague(?League $league): self
    {
        $this->league = $league;

        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): self
    {
        $this->logoPath = $logoPath !== null ? trim($logoPath) : null;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->isActive();
    }

    public function setActive(bool $active): self
    {
        return $this->setIsActive($active);
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
    public function getHomeFixtures(): Collection
    {
        return $this->homeFixtures;
    }

    /**
     * @return Collection<int, Fixture>
     */
    public function getAwayFixtures(): Collection
    {
        return $this->awayFixtures;
    }
}
