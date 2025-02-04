<?php

namespace App\Entity;

use App\Repository\SubjectEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]
#[ORM\Entity(repositoryClass: SubjectEntityRepository::class)]
class SubjectEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, NonAttendanceEntity>
     */
    #[ORM\OneToMany(targetEntity: NonAttendanceEntity::class, mappedBy: 'relatedStudent')]
    private Collection $studentNonattendances;

    /**
     * @var Collection<int, AssignmentEntity>
     */
    #[ORM\OneToMany(targetEntity: AssignmentEntity::class, mappedBy: 'relatedstudent')]
    private Collection $studentAssignments;

    public function __construct()
    {
        $this->studentNonattendances = new ArrayCollection();
        $this->studentAssignments = new ArrayCollection();
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

    /**
     * @return Collection<int, NonAttendanceEntity>
     */
    public function getStudentNonattendances(): Collection
    {
        return $this->studentNonattendances;
    }

    public function addStudentNonattendance(NonAttendanceEntity $studentNonattendance): static
    {
        if (!$this->studentNonattendances->contains($studentNonattendance)) {
            $this->studentNonattendances->add($studentNonattendance);
            $studentNonattendance->setRelatedStudent($this);
        }

        return $this;
    }

    public function removeStudentNonattendance(NonAttendanceEntity $studentNonattendance): static
    {
        if ($this->studentNonattendances->removeElement($studentNonattendance)) {
            // set the owning side to null (unless already changed)
            if ($studentNonattendance->getRelatedStudent() === $this) {
                $studentNonattendance->setRelatedStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AssignmentEntity>
     */
    public function getStudentAssignments(): Collection
    {
        return $this->studentAssignments;
    }

    public function addStudentAssignment(AssignmentEntity $studentAssignment): static
    {
        if (!$this->studentAssignments->contains($studentAssignment)) {
            $this->studentAssignments->add($studentAssignment);
            $studentAssignment->setRelatedstudent($this);
        }

        return $this;
    }

    public function removeStudentAssignment(AssignmentEntity $studentAssignment): static
    {
        if ($this->studentAssignments->removeElement($studentAssignment)) {
            // set the owning side to null (unless already changed)
            if ($studentAssignment->getRelatedstudent() === $this) {
                $studentAssignment->setRelatedstudent(null);
            }
        }

        return $this;
    }
}
