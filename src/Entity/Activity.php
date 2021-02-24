<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $estimated_value;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="createdActivities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="acceptedActivities")
     * @ORM\JoinColumn(nullable=true)
     */
    private $acceptedUser;

    /**
     * @ORM\ManyToOne(targetEntity=ActivityCategory::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=ActivityType::class, inversedBy="activities")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createTimestamp;

    /**
     * @ORM\OneToMany(targetEntity=ActivityMatch::class, mappedBy="activity", orphanRemoval=true)
     */
    private $activityMatches;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $time;

    /**
     * @ORM\OneToMany(targetEntity=ActivityComment::class, mappedBy="activity")
     */
    private $activityComments;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="activity")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="activity")
     */
    private $notifications;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @ORM\Column(type="boolean", options={"default":0})
     */
    private $rated = 0;

    public function __construct()
    {
        $this->activityMatches = new ArrayCollection();
        $this->activityComments = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->location = "Pavia";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEstimatedValue(): ?int
    {
        return $this->estimated_value;
    }

    public function setEstimatedValue(?int $estimated_value): self
    {
        $this->estimated_value = $estimated_value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getAcceptedUser(): ?User
    {
        return $this->acceptedUser;
    }

    public function setAcceptedUser(?User $acceptedUser): self
    {
        $this->acceptedUser = $acceptedUser;

        return $this;
    }

    public function getCategory(): ?ActivityCategory
    {
        return $this->category;
    }

    public function setCategory(?ActivityCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getType(): ?ActivityType
    {
        return $this->type;
    }

    public function setType(?ActivityType $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|ActivityMatch[]
     */
    public function getActivityMatches(): Collection
    {
        return $this->activityMatches;
    }

    public function addActivityMatch(ActivityMatch $activityMatch): self
    {
        if (!$this->activityMatches->contains($activityMatch)) {
            $this->activityMatches[] = $activityMatch;
            $activityMatch->setActivity($this);
        }

        return $this;
    }

    public function removeActivityMatch(ActivityMatch $activityMatch): self
    {
        if ($this->activityMatches->contains($activityMatch)) {
            $this->activityMatches->removeElement($activityMatch);
            // set the owning side to null (unless already changed)
            if ($activityMatch->getActivity() === $this) {
                $activityMatch->setActivity(null);
            }
        }

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return Collection|ActivityComment[]
     */
    public function getActivityComments(): Collection
    {
        return $this->activityComments;
    }

    public function addActivityComment(ActivityComment $activityComment): self
    {
        if (!$this->activityComments->contains($activityComment)) {
            $this->activityComments[] = $activityComment;
            $activityComment->setActivity($this);
        }

        return $this;
    }

    public function removeActivityComment(ActivityComment $activityComment): self
    {
        if ($this->activityComments->contains($activityComment)) {
            $this->activityComments->removeElement($activityComment);
            // set the owning side to null (unless already changed)
            if ($activityComment->getActivity() === $this) {
                $activityComment->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions[] = $transaction;
            $transaction->setActivity($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getActivity() === $this) {
                $transaction->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setActivity($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getActivity() === $this) {
                $notification->setActivity(null);
            }
        }

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getRated(): ?bool
    {
        return $this->rated;
    }

    public function setRated(bool $rated): self
    {
        $this->rated = $rated;

        return $this;
    }
}
