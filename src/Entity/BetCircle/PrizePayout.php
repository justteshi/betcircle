<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\PrizePayoutStatus;
use App\Enum\BetCircle\PrizePayoutType;
use App\Repository\BetCircle\PrizePayoutRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: PrizePayoutRepository::class)]
#[ORM\Table(name: 'betcircle_prize_payout')]
#[ORM\HasLifecycleCallbacks]
class PrizePayout implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\ManyToOne(targetEntity: Season::class)]
    #[ORM\JoinColumn(name: 'season_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Season $season = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class)]
    #[ORM\JoinColumn(name: 'game_week_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?GameWeek $gameWeek = null;

    #[ORM\Column(length: 20, enumType: PrizePayoutType::class)]
    private PrizePayoutType $type;

    #[ORM\Column]
    private int $amountTokens = 0;

    #[ORM\Column(length: 20, enumType: PrizePayoutStatus::class)]
    private PrizePayoutStatus $status;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $availableAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $requestedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $approvedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $paidAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();

        $this->type = PrizePayoutType::WEEKLY;
        $this->status = PrizePayoutStatus::AVAILABLE;
        $this->availableAt = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;

        if (!isset($this->availableAt)) {
            $this->availableAt = $now;
        }
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

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): self
    {
        $this->customer = $customer;

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

    public function getType(): PrizePayoutType
    {
        return $this->type;
    }

    public function getTypeValue(): string
    {
        return $this->type->value;
    }

    public function setType(PrizePayoutType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getAmountTokens(): int
    {
        return $this->amountTokens;
    }

    public function setAmountTokens(int $amountTokens): self
    {
        $this->amountTokens = $amountTokens;

        return $this;
    }

    public function getStatus(): PrizePayoutStatus
    {
        return $this->status;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatus(PrizePayoutStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAvailableAt(): \DateTimeImmutable
    {
        return $this->availableAt;
    }

    public function setAvailableAt(\DateTimeImmutable $availableAt): self
    {
        $this->availableAt = $availableAt;

        return $this;
    }

    public function getRequestedAt(): ?\DateTimeImmutable
    {
        return $this->requestedAt;
    }

    public function setRequestedAt(?\DateTimeImmutable $requestedAt): self
    {
        $this->requestedAt = $requestedAt;

        return $this;
    }

    public function getApprovedAt(): ?\DateTimeImmutable
    {
        return $this->approvedAt;
    }

    public function setApprovedAt(?\DateTimeImmutable $approvedAt): self
    {
        $this->approvedAt = $approvedAt;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeImmutable
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeImmutable $paidAt): self
    {
        $this->paidAt = $paidAt;

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
}
