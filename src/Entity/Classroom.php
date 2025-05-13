<?php

namespace App\Entity;

use App\Repository\ClassroomRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassroomRepository::class)]
class Classroom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;


    #[ORM\ManyToOne(inversedBy: 'Classroom')]
    #[ORM\JoinColumn(name: 'TEACHER_ID', nullable: false)]
    private ?User $TeacherId = null;


    #-----------------------------------------------------------

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

    public function getTeacherId(): ?User
    {
        return $this->TeacherId;
    }

    public function setTeacherId(?User $TeacherId): void
    {
        $this->TeacherId = $TeacherId;
    }
}
