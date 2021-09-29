<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
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
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $draft;

    /**
     * @ORM\Column(type="boolean")
     */
    private $done;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateClose;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     */
    private $user;

    public function __construct()
    {
        $this->setDateAdd(new DateTime()) ;
        $this->setDateUpd(new DateTime());
        $this->setDateClose(new DateTime());
        $this->setDraft(true);
        $this->setDone(false);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDraft(): ?bool
    {
        return $this->draft;
    }

    /**
     * @param bool $draft
     * @return $this
     */
    public function setDraft(bool $draft): self
    {
        $this->draft = $draft;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getDone(): ?bool
    {
        return $this->done;
    }

    /**
     * @param bool $done
     * @return $this
     */
    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    /**
     * @param \DateTimeInterface $dateAdd
     * @return $this
     */
    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateUpd(): ?\DateTimeInterface
    {
        return $this->dateUpd;
    }

    /**
     * @param \DateTimeInterface $dateUpd
     * @return $this
     */
    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateClose(): ?\DateTimeInterface
    {
        return $this->dateClose;
    }

    /**
     * @param \DateTimeInterface $dateClose
     * @return $this
     */
    public function setDateClose(\DateTimeInterface $dateClose): self
    {
        $this->dateClose = $dateClose;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
