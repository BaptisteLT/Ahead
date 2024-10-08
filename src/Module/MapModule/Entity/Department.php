<?php
namespace App\Module\MapModule\Entity;

use App\Entity\Trait\CreateUpdateTrait;
use App\Module\ConditionModule\Entity\Report;
use Doctrine\ORM\Mapping as ORM;
use App\Module\MapModule\Repository\DepartmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
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

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'department', orphanRemoval: true)]
    private Collection $reports;

    #[ORM\ManyToOne(targetEntity: Region::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Region $region = null;

    public function __construct()
    {
        $this->reports = new ArrayCollection();
    }

    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
            $report->setDepartment($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            if ($report->getDepartment() === $this) {
                $report->setDepartment(null);
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): static
    {
        $this->region = $region;
        return $this;
    }
}
