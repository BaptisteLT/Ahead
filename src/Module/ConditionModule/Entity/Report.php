<?php
namespace App\Module\ConditionModule\Entity;

use App\Entity\Trait\CreateUpdateTrait;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Module\ConditionModule\Repository\ReportRepository;
use App\Module\MapModule\Entity\Department;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    use CreateUpdateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Disease $disease = null;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(nullable: false)] // Not nullable to ensure every report has a department
    private ?Department $department = null;

    #[ORM\ManyToMany(targetEntity: Symptoms::class, inversedBy: 'reports')]
    private Collection $symptoms;

    
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateReport = null;
    
    public function __construct() {
        $this->symptoms = new ArrayCollection();
    }

    
    #[ORM\Column(nullable: true)]
    private ?bool $hasAcceptedRgpd = null;
    
    public function getHasAcceptedRgpd(): ?bool{
        return $this->hasAcceptedRgpd;
    }
    
    public function setHasAcceptedRgpd(?bool $hasAcceptedRgpd): static{
        $this->hasAcceptedRgpd = $hasAcceptedRgpd;
    
        return $this;
    }
    public function getDateReport(): ?\DateTimeImmutable{
        return $this->dateReport;
    }
    
    public function setDateReport(?\DateTimeImmutable $dateReport): static{
        $this->dateReport = $dateReport;
    
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    
    public function getDisease(): ?Disease
    {
        return $this->disease;
    }

    public function setDisease(?Disease $disease): static
    {
        $this->disease = $disease;

        return $this;
    }

    
    
    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): static
    {
        $this->department = $department;

        return $this;
    }
    
    
    /**
     * @return Collection<int, Symptoms>
     */
    public function getSymptoms(): Collection
    {
        return $this->symptoms;
    }
    public function addSymptom(Symptoms $symptom): static {
        if (!$this->symptoms->contains($symptom)) {
            $this->symptoms->add($symptom);
            $symptom->addReport($this); // Maintain the inverse relationship
        }
        return $this;
    }

    public function removeSymptom(Symptoms $symptom): static {
        if ($this->symptoms->removeElement($symptom)) {
            $symptom->removeReport($this); // Maintain the inverse relationship
        }
        return $this;
    }
}