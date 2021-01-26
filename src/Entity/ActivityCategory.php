<?php

namespace App\Entity;

use App\Repository\ActivityCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActivityCategoryRepository::class)
 */
class ActivityCategory
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
     * @ORM\OneToMany(targetEntity=Activity::class, mappedBy="category")
     */
    private $activities;

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

    public function setName(string $nameIT): self
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
            $activity->setCategory($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->contains($activity)) {
            $this->activities->removeElement($activity);
            // set the owning side to null (unless already changed)
            if ($activity->getCategory() === $this) {
                $activity->setCategory(null);
            }
        }

        return $this;
    }
}
