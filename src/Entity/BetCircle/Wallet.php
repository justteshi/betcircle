<?php

declare(strict_types=1);

namespace App\Entity\BetCircle;

use App\Repository\BetCircle\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

#[ORM\Entity(repositoryClass: WalletRepository::class)]
#[ORM\Table(name: 'betcircle_wallet')]
#[ORM\UniqueConstraint(name: 'uniq_betcircle_wallet_customer', columns: ['customer_id'])]
#[ORM\HasLifecycleCallbacks]
class Wallet implements ResourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: CustomerInterface::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CustomerInterface $customer = null;

    #[ORM\Column]
    private int $balance = 0;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(targetEntity: WalletTransaction::class, mappedBy: 'wallet', cascade: ['persist'], orphanRemoval: false)]
    #[ORM\OrderBy(['createdAt' => 'DESC', 'id' => 'DESC'])]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();

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

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function setBalance(int $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    public function increaseBalance(int $amount): self
    {
        $this->balance += $amount;

        return $this;
    }

    public function decreaseBalance(int $amount): self
    {
        $this->balance -= $amount;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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
     * @return Collection<int, WalletTransaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(WalletTransaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setWallet($this);
        }

        return $this;
    }

    public function removeTransaction(WalletTransaction $transaction): self
    {
        $this->transactions->removeElement($transaction);

        return $this;
    }
}
