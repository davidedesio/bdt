<?php

namespace App\Entity;

use App\Repository\BankRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BankRepository::class)
 */
class Bank
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cluster;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender_email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sender_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contact_email;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCluster(): ?string
    {
        return $this->cluster;
    }

    public function setCluster(string $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    public function getSenderEmail(): ?string
    {
        return $this->sender_email;
    }

    public function setSenderEmail(string $sender_email): self
    {
        $this->sender_email = $sender_email;

        return $this;
    }

    public function getSenderName(): ?string
    {
        return $this->sender_name;
    }

    public function setSenderName(string $sender_name): self
    {
        $this->sender_name = $sender_name;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contact_email;
    }

    public function setContactEmail(string $contact_email): self
    {
        $this->contact_email = $contact_email;

        return $this;
    }
}
