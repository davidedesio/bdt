<?php

namespace App\Entity;

use App\Repository\ActivityMatchRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityMatchRepository::class)
 */
class ActivityMatch
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $createUser;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="activityMatches")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createTimestamp;

    public function getId(): ?int
    {
        return $this->id;
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
}
