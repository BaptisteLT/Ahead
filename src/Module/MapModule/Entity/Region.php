<?php
namespace App\Module\MapModule\Entity;

use App\Entity\Trait\CreateUpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Module\MapModule\Repository\RegionRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: RegionRepository::class)]
class Region
{
    use CreateUpdateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $number = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    private ?string $svgX = null;

    private ?string $svgY = null;

    /**
     * @var Collection<int, Department>
     */
    #[ORM\OneToMany(targetEntity: Department::class, mappedBy: 'region', orphanRemoval: true)]
    private Collection $departments;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
    }

    
    #[ORM\Column(nullable: true)]
    private ?int $nbResidents = null;
    
    public function getNbResidents(): ?int{
        return $this->nbResidents;
    }
    
    public function setNbResidents(?int $nbResidents): static{
        $this->nbResidents = $nbResidents;
    
        return $this;
    }
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): static
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->setRegion($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): static
    {
        if ($this->departments->removeElement($department)) {
            if ($department->getRegion() === $this) {
                $department->setRegion(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): static
    {
        $this->number = $number;
        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSvgY(): ?string
    {
        return $this->svgY;
    }

    public function setSvgY(?string $svgY): static
    {
        $this->svgY = $svgY;
        return $this;
    }
    
    public function getSvgX(): ?string
    {
        return $this->svgX;
    }

    public function setSvgX(?string $svgX): static
    {
        $this->svgX = $svgX;
        return $this;
    }
}
