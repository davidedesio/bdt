<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $userFrom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="receivedTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userTo;

    /**
     * @ORM\Column(type="float")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createTimestamp;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createUser;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $reason;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFrom(): ?User
    {
        return $this->userFrom;
    }

    public function setUserFrom(?User $userFrom): self
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getUserTo(): ?User
    {
        return $this->userTo;
    }

    public function setUserTo(?User $userTo): self
    {
        $this->userTo = $userTo;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getCreateTimestamp(): ?\DateTimeInterface
    {
        return $this->createTimestamp;
    }

    public function setCreateTimestamp(\DateTimeInterface $createTimestamp): self
    {
        $this->createTimestamp = $createTimestamp;

        return $this;
    }

    public function getCreateUser(): ?User
    {
        return $this->createUser;
    }

    public function setCreateUser(?User $createUser): self
    {
        $this->createUser = $createUser;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }
}
