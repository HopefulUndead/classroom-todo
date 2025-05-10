<?php

namespace App\Entity;

use App\Repository\UserClassromRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserClassromRepository::class)]
class UserClassrom
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idClassroom = null;

    #[ORM\Column]
    private ?int $idUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClassroom(): ?int
    {
        return $this->idClassroom;
    }

    public function setIdClassroom(int $idClassroom): static
    {
        $this->idClassroom = $idClassroom;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }
}
