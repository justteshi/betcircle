<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Repository\BetCircle\WalletTransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use App\Enum\BetCircle\WalletTransactionType;
use App\Enum\BetCircle\WalletTransactionDirection;

#[ORM\Entity(repositoryClass: WalletTransactionRepository::class)]
#[ORM\Table(name: 'betcircle_wallet_transaction')]
#[ORM\HasLifecycleCallbacks]
class WalletTransaction implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Wallet::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Wallet $wallet = null;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\Column(length: 50, enumType: WalletTransactionType::class)]
    private WalletTransactionType $type;

    #[ORM\Column(length: 10, enumType: WalletTransactionDirection::class)]
    private WalletTransactionDirection $direction;

    #[ORM\Column]
    private int $amount = 0;

    #[ORM\Column]
    private int $balanceBefore = 0;

    #[ORM\Column]
    private int $balanceAfter = 0;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $referenceType = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $referenceId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $metadata = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        $this->type = WalletTransactionType::CORRECTION;
        $this->direction = WalletTransactionDirection::CREDIT;
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

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

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

    public function getType(): WalletTransactionType
    {
        return $this->type;
    }

    public function getTypeValue(): string
    {
        return $this->type->value;
    }

    public function setType(WalletTransactionType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDirection(): WalletTransactionDirection
    {
        return $this->direction;
    }

    public function getDirectionValue(): string
    {
        return $this->direction->value;
    }

    public function setDirection(WalletTransactionDirection $direction): self
    {
        $this->direction = $direction;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getBalanceBefore(): int
    {
        return $this->balanceBefore;
    }

    public function setBalanceBefore(int $balanceBefore): self
    {
        $this->balanceBefore = $balanceBefore;

        return $this;
    }

    public function getBalanceAfter(): int
    {
        return $this->balanceAfter;
    }

    public function setBalanceAfter(int $balanceAfter): self
    {
        $this->balanceAfter = $balanceAfter;

        return $this;
    }

    public function getReferenceType(): ?string
    {
        return $this->referenceType;
    }

    public function setReferenceType(?string $referenceType): self
    {
        $this->referenceType = $referenceType !== null ? trim($referenceType) : null;

        return $this;
    }

    public function getReferenceId(): ?string
    {
        return $this->referenceId;
    }

    public function setReferenceId(?string $referenceId): self
    {
        $this->referenceId = $referenceId !== null ? trim($referenceId) : null;

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

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
