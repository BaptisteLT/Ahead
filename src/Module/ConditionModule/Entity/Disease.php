<?php
namespace App\Module\ConditionModule\Entity;

use App\Entity\Trait\CreateUpdateTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Module\ConditionModule\Repository\DiseaseRepository;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DiseaseRepository::class)]
class Disease
{
    use CreateUpdateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Report>
     */
    #[ORM\OneToMany(targetEntity: Report::class, mappedBy: 'disease', orphanRemoval: true)]
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
            $report->setDisease($this);
        }

        return $this;
    }

    public function removeReport(Report $report): static
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getDisease() === $this) {
                $report->setDisease(null);
            }
        }

        return $this;
    }
}