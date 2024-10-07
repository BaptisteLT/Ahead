<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;








    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $decimall = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDecimall(): ?string
    {
        return $this->decimall;
    }

    public function setDecimall(?string $decimall): static
    {
        $this->decimall = $decimall;

        return $this;
    }

}
