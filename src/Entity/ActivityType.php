<?php

namespace App\Entity;

use App\Repository\ActivityTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityTypeRepository::class)
 */
class ActivityType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameIT;

    /**
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="type")
     */
    private $activities;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    public function __construct()
    {
        $this->activities = new ArrayCollection();
    }

    public function __toString(): String
    {
        return $this->nameIT;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameIT(): ?string
    {
        return $this->nameIT;
    }

    public function setNameIT(string $nameIT): self
    {
        $this->nameIT = $nameIT;

        return $this;
    }

    /**
     * @return Collection|Activity[]
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->setType($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getType() === $this) {
                $activity->setType(null);
            }
        }

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
