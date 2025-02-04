<?php
declare(strict_types=1);
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTimeImmutable;
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
class StudentEntity extends User {
    private DateTimeImmutable $bornDate;

    /**
     * @var Collection<int, ScoreEntity>
     */
    #[ORM\OneToMany(targetEntity: ScoreEntity::class, mappedBy: 'studentEntity')]
    private Collection $studentsScores;

    #[ORM\ManyToOne(inversedBy: 'folowStudents')]
    private ?ParentEntity $parentofStudent = null;

    #[ORM\ManyToOne(inversedBy: 'studentOfClass')]
    #[ORM\JoinColumn(nullable: true)]
    private ?ClassEntity $studentClassName = null;

    public function __construct()
    {
        parent::__construct();
        $this->studentsScores = new ArrayCollection();
    }

    public function getBornDate(): DateTimeImmutable {
        return $this->bornDate;
    }

    public function setBornDate(DateTimeImmutable $bornDate): Static {
        $this->bornDate = $bornDate;
        return $this;
    }

    /**
     * @return Collection<int, ScoreEntity>
     */
    public function getStudentsScores(): Collection
    {
        return $this->studentsScores;
    }

    public function addStudentsScore(ScoreEntity $studentsScore): static
    {
        if (!$this->studentsScores->contains($studentsScore)) {
            $this->studentsScores->add($studentsScore);
            $studentsScore->setStudentEntity($this);
        }

        return $this;
    }

    public function removeStudentsScore(ScoreEntity $studentsScore): static
    {
        if ($this->studentsScores->removeElement($studentsScore)) {
            // set the owning side to null (unless already changed)
            if ($studentsScore->getStudentEntity() === $this) {
                $studentsScore->setStudentEntity(null);
            }
        }

        return $this;
    }

    public function getParentofStudent(): ?ParentEntity
    {
        return $this->parentofStudent;
    }

    public function setParentofStudent(?ParentEntity $parentofStudent): static
    {
        $this->parentofStudent = $parentofStudent;

        return $this;
    }

    public function getStudentClassName(): ?ClassEntity
    {
        return $this->studentClassName;
    }

    public function setStudentClassName(?ClassEntity $studentClassName): static
    {
        $this->studentClassName = $studentClassName;

        return $this;
    }

}