<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?bool $completed = null;

    #[ORM\ManyToOne(inversedBy: 'Task')]
    #[ORM\JoinColumn(name: 'USER_ID', nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $date = null;
    #[ORM\ManyToOne(inversedBy: 'Task')]
    #[ORM\JoinColumn(name: 'CLASSROOM_ID', nullable: false)]
    private ?Classroom $classId = null;


    # --------------------
    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): void
    {
        $this->userId = $userId;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): static
    {
        $this->completed = $completed;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getClassId(): ?Classroom
    {
        return $this->classId;
    }

    public function setClassId(?Classroom $classId): void
    {
        $this->classId = $classId;
    }
}
