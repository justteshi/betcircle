<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Enum\BetCircle\WeekEntryStatus;
use App\Repository\BetCircle\WeekEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: WeekEntryRepository::class)]
#[ORM\Table(name: 'betcircle_week_entry')]
#[ORM\UniqueConstraint(name: 'uniq_betcircle_week_entry_game_week_customer', columns: ['game_week_id', 'customer_id'])]
#[ORM\HasLifecycleCallbacks]
class WeekEntry implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: GameWeek::class)]
    #[ORM\JoinColumn(name: 'game_week_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?GameWeek $gameWeek = null;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\OneToOne(targetEntity: WalletTransaction::class)]
    #[ORM\JoinColumn(name: 'wallet_transaction_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?WalletTransaction $walletTransaction = null;

    #[ORM\Column(length: 20, enumType: WeekEntryStatus::class)]
    private WeekEntryStatus $status;

    #[ORM\Column]
    private int $entryCostTokens = 0;

    #[ORM\Column]
    private int $weeklyContributionTokens = 0;

    #[ORM\Column]
    private int $seasonalContributionTokens = 0;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $joinedAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();

        $this->status = WeekEntryStatus::CONFIRMED;
        $this->joinedAt = $now;
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;

        if (!isset($this->joinedAt)) {
            $this->joinedAt = $now;
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

    public function getWalletTransaction(): ?WalletTransaction
    {
        return $this->walletTransaction;
    }

    public function setWalletTransaction(?WalletTransaction $walletTransaction): self
    {
        $this->walletTransaction = $walletTransaction;

        return $this;
    }

    public function getStatus(): WeekEntryStatus
    {
        return $this->status;
    }

    public function getStatusValue(): string
    {
        return $this->status->value;
    }

    public function setStatus(WeekEntryStatus $status): self
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

    public function getWeeklyContributionTokens(): int
    {
        return $this->weeklyContributionTokens;
    }

    public function setWeeklyContributionTokens(int $weeklyContributionTokens): self
    {
        $this->weeklyContributionTokens = $weeklyContributionTokens;

        return $this;
    }

    public function getSeasonalContributionTokens(): int
    {
        return $this->seasonalContributionTokens;
    }

    public function setSeasonalContributionTokens(int $seasonalContributionTokens): self
    {
        $this->seasonalContributionTokens = $seasonalContributionTokens;

        return $this;
    }

    public function getJoinedAt(): \DateTimeImmutable
    {
        return $this->joinedAt;
    }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): self
    {
        $this->joinedAt = $joinedAt;

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

    public function isConfirmed(): bool
    {
        return $this->status === WeekEntryStatus::CONFIRMED;
    }
}
