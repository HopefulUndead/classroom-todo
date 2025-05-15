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

    /**
     * @var Collection <int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'classrooms')] // propriétaire car pas mapping
    // #[ORM\JoinTable(name: 'user_classroom')] possibilité de spécifier nom mais par défaut 'proprio_subordoné'
    private Collection $usersInClassroom;

    #-----------------------------------------------------------
    public function getUsersInClassroom(): Collection
    {
        return $this->usersInClassroom;
    }

    public function setUsersInClassroom(Collection $usersInClassroom): void
    {
        $this->usersInClassroom = $usersInClassroom;
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

    public function getTeacherId(): ?User
    {
        return $this->TeacherId;
    }

    public function setTeacherId(?User $TeacherId): void
    {
        $this->TeacherId = $TeacherId;
    }
}
