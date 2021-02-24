<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="EMAIL_EXISTS")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fiscal_code;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $zip_code;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $job;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $hobbies;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="users")
     */
    private $tags;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birth_date;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $driving_licence;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $privacy_email;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $privacy_phone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $privacy_address;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $terms_acceptance;

    /**
     * @ORM\ManyToMany(targetEntity=Skill::class, inversedBy="users")
     */
    private $skills;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userFrom", orphanRemoval=true)
     */
    private $sentTransactions;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="userTo", orphanRemoval=true)
     */
    private $receivedTransactions;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="createUser", orphanRemoval=true)
     */
    private $createdActivities;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="acceptedUser")
     */
    private $acceptedActivities;

    /**
     * @ORM\OneToMany(targetEntity=Transaction::class, mappedBy="createUser")
     */
    private $transactions;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
     */
    private $notifications;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $_del;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createTimestamp;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    private $commentsEmail = 1;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default":1})
     */
    private $activitiesEmail = 1;

    /**
     * @ORM\Column(type="integer", options={"default":0})
     */
    private $rating = 0;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->sentTransactions = new ArrayCollection();
        $this->receivedTransactions = new ArrayCollection();
        $this->createdActivities = new ArrayCollection();
        $this->acceptedActivities = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function __toString(): String
    {
        return $this->name." ".$this->surname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getFiscalCode(): ?string
    {
        return $this->fiscal_code;
    }

    public function setFiscalCode(?string $fiscal_code): self
    {
        $this->fiscal_code = $fiscal_code;

        return $this;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zip_code;
    }

    public function setZipCode(?string $zip_code): self
    {
        $this->zip_code = $zip_code;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): self
    {
        $this->job = $job;

        return $this;
    }

    public function getHobbies(): ?string
    {
        return $this->hobbies;
    }

    public function setHobbies(?string $hobbies): self
    {
        $this->hobbies = $hobbies;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tags): self
    {
        if (!$this->tags->contains($tags)) {
            $this->tags[] = $tags;
        }

        return $this;
    }

    public function removeTag(Tag $tags): self
    {
        if ($this->tag->contains($tags)) {
            $this->tag->removeElement($tags);
        }

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(?\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDrivingLicence(): ?bool
    {
        return $this->driving_licence;
    }

    public function setDrivingLicence(bool $driving_licence): self
    {
        $this->driving_licence = $driving_licence;

        return $this;
    }

    public function getPrivacyEmail(): ?bool
    {
        return $this->privacy_email;
    }

    public function setPrivacyEmail(?bool $privacy_email): self
    {
        $this->privacy_email = $privacy_email;

        return $this;
    }

    public function getPrivacyPhone(): ?bool
    {
        return $this->privacy_phone;
    }

    public function setPrivacyPhone(?bool $privacy_phone): self
    {
        $this->privacy_phone = $privacy_phone;

        return $this;
    }

    public function getPrivacyAddress(): ?bool
    {
        return $this->privacy_address;
    }

    public function setPrivacyAddress(?bool $privacy_address): self
    {
        $this->privacy_address = $privacy_address;

        return $this;
    }

    public function getTermsAcceptance(): ?bool
    {
        return $this->terms_acceptance;
    }

    public function setTermsAcceptance(?bool $terms_acceptance): self
    {
        $this->terms_acceptance = $terms_acceptance;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
        }

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getSentTransactions(): Collection
    {
        return $this->sentTransactions;
    }

    public function addSentTransaction(Transaction $sentTransaction): self
    {
        if (!$this->sentTransactions->contains($sentTransaction)) {
            $this->sentTransactions[] = $sentTransaction;
            $sentTransaction->setUserFrom($this);
        }

        return $this;
    }

    public function removeSentTransaction(Transaction $sentTransaction): self
    {
        if ($this->sentTransactions->contains($sentTransaction)) {
            $this->sentTransactions->removeElement($sentTransaction);
            // set the owning side to null (unless already changed)
            if ($sentTransaction->getUserFrom() === $this) {
                $sentTransaction->setUserFrom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getReceivedTransactions(): Collection
    {
        return $this->receivedTransactions;
    }

    public function addReceivedTransaction(Transaction $receivedTransaction): self
    {
        if (!$this->receivedTransactions->contains($receivedTransaction)) {
            $this->receivedTransactions[] = $receivedTransaction;
            $receivedTransaction->setUserTo($this);
        }

        return $this;
    }

    public function removeReceivedTransaction(Transaction $receivedTransaction): self
    {
        if ($this->receivedTransactions->contains($receivedTransaction)) {
            $this->receivedTransactions->removeElement($receivedTransaction);
            // set the owning side to null (unless already changed)
            if ($receivedTransaction->getUserTo() === $this) {
                $receivedTransaction->setUserTo(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getCreatedActivities(): Collection
    {
        return $this->createdActivities;
    }

    public function addCreatedActivity(Activity $createdActivity): self
    {
        if (!$this->createdActivities->contains($createdActivity)) {
            $this->createdActivities[] = $createdActivity;
            $createdActivity->setCreateUser($this);
        }

        return $this;
    }

    public function removeCreatedActivity(Activity $createdActivity): self
    {
        if ($this->createdActivities->contains($createdActivity)) {
            $this->createdActivities->removeElement($createdActivity);
            // set the owning side to null (unless already changed)
            if ($createdActivity->getCreateUser() === $this) {
                $createdActivity->setCreateUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getAcceptedActivities(): Collection
    {
        return $this->acceptedActivities;
    }

    public function addAcceptedActivity(Activity $acceptedActivity): self
    {
        if (!$this->acceptedActivities->contains($acceptedActivity)) {
            $this->acceptedActivities[] = $acceptedActivity;
            $acceptedActivity->setAcceptedUser($this);
        }

        return $this;
    }

    public function removeAcceptedActivity(Activity $acceptedActivity): self
    {
        if ($this->acceptedActivities->contains($acceptedActivity)) {
            $this->acceptedActivities->removeElement($acceptedActivity);
            // set the owning side to null (unless already changed)
            if ($acceptedActivity->getAcceptedUser() === $this) {
                $acceptedActivity->setAcceptedUser(null);
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
            $transaction->setCreateUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
            // set the owning side to null (unless already changed)
            if ($transaction->getCreateUser() === $this) {
                $transaction->setCreateUser(null);
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
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getDel(): ?int
    {
        return $this->_del;
    }

    public function setDel(int $_del): self
    {
        $this->_del = $_del;

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

    public function getCommentsEmail(): ?bool
    {
        return $this->commentsEmail;
    }

    public function setCommentsEmail(bool $commentsEmail): self
    {
        $this->commentsEmail = $commentsEmail;

        return $this;
    }

    public function getActivitiesEmail(): ?bool
    {
        return $this->activitiesEmail;
    }

    public function setActivitiesEmail(bool $activitiesEmail): self
    {
        $this->activitiesEmail = $activitiesEmail;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
