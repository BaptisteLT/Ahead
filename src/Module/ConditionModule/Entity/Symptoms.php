<?php
namespace App\Module\ConditionModule\Entity;

use App\Entity\Trait\CreateUpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Module\ConditionModule\Repository\SymptomsRepository;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: SymptomsRepository::class)]
class Symptoms
{
    use CreateUpdateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\ManyToMany(targetEntity: Report::class, mappedBy: 'symptoms')]
    private Collection $reports;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    
    /**
     * @return Collection<int, Test>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(Report $report): static
    {
        if (!$this->reports->contains($report)) {
            $this->reports->add($report);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        $this->reports->removeElement($report);

        return $this;
    }
}