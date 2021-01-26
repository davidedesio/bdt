<?php

namespace App\Entity;

use App\Repository\ActivityCommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityCommentRepository::class)
 */
class ActivityComment
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
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="activityComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createTimestamp;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
