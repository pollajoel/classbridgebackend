<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]
#[ORM\Entity()]

class TeacherEntity extends User
{
    /**
     * @var Collection<int, ScoreEntity>
     */
    #[ORM\OneToMany(targetEntity: ScoreEntity::class, mappedBy: 'relatedTeacher')]
    private Collection $AssignScores;

    /**
     * @var Collection<int, AssignmentEntity>
     */
    #[ORM\OneToMany(targetEntity: AssignmentEntity::class, mappedBy: 'teacherWhoAssign')]
    private Collection $teacherCreatedAssignments;

    /**
     * @var Collection<int, NonAttendanceEntity>
     */
    #[ORM\OneToMany(targetEntity: NonAttendanceEntity::class, mappedBy: 'nonAttendanceTeacher')]
    private Collection $teacherCreatedNonAttendances;

    #[ORM\ManyToOne(inversedBy: 'listOfTeachers')]
    private ?ClassEntity $nameOfClass = null;

    public function __construct()
    {
        parent::__construct();
        $this->AssignScores = new ArrayCollection();
        $this->teacherCreatedAssignments = new ArrayCollection();
        $this->teacherCreatedNonAttendances = new ArrayCollection();
    }

    /**
     * @return Collection<int, ScoreEntity>
     */
    public function getAssignScores(): Collection
    {
        return $this->AssignScores;
    }

    public function addAssignScore(ScoreEntity $assignScore): static
    {
        if (!$this->AssignScores->contains($assignScore)) {
            $this->AssignScores->add($assignScore);
            $assignScore->setRelatedTeacher($this);
        }

        return $this;
    }

    public function removeAssignScore(ScoreEntity $assignScore): static
    {
        if ($this->AssignScores->removeElement($assignScore)) {
            // set the owning side to null (unless already changed)
            if ($assignScore->getRelatedTeacher() === $this) {
                $assignScore->setRelatedTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AssignmentEntity>
     */
    public function getTeacherCreatedAssignments(): Collection
    {
        return $this->teacherCreatedAssignments;
    }

    public function addTeacherCreatedAssignment(AssignmentEntity $teacherCreatedAssignment): static
    {
        if (!$this->teacherCreatedAssignments->contains($teacherCreatedAssignment)) {
            $this->teacherCreatedAssignments->add($teacherCreatedAssignment);
            $teacherCreatedAssignment->setTeacherWhoAssign($this);
        }

        return $this;
    }

    public function removeTeacherCreatedAssignment(AssignmentEntity $teacherCreatedAssignment): static
    {
        if ($this->teacherCreatedAssignments->removeElement($teacherCreatedAssignment)) {
            // set the owning side to null (unless already changed)
            if ($teacherCreatedAssignment->getTeacherWhoAssign() === $this) {
                $teacherCreatedAssignment->setTeacherWhoAssign(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, NonAttendanceEntity>
     */
    public function getTeacherCreatedNonAttendances(): Collection
    {
        return $this->teacherCreatedNonAttendances;
    }

    public function addTeacherCreatedNonAttendance(NonAttendanceEntity $teacherCreatedNonAttendance): static
    {
        if (!$this->teacherCreatedNonAttendances->contains($teacherCreatedNonAttendance)) {
            $this->teacherCreatedNonAttendances->add($teacherCreatedNonAttendance);
            $teacherCreatedNonAttendance->setNonAttendanceTeacher($this);
        }

        return $this;
    }

    public function removeTeacherCreatedNonAttendance(NonAttendanceEntity $teacherCreatedNonAttendance): static
    {
        if ($this->teacherCreatedNonAttendances->removeElement($teacherCreatedNonAttendance)) {
            // set the owning side to null (unless already changed)
            if ($teacherCreatedNonAttendance->getNonAttendanceTeacher() === $this) {
                $teacherCreatedNonAttendance->setNonAttendanceTeacher(null);
            }
        }

        return $this;
    }

    public function getNameOfClass(): ?ClassEntity
    {
        return $this->nameOfClass;
    }

    public function setNameOfClass(?ClassEntity $nameOfClass): static
    {
        $this->nameOfClass = $nameOfClass;

        return $this;
    }
}

