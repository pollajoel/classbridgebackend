<?php

namespace App\Entity;

use App\Repository\ClassEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;



#[ORM\Entity(repositoryClass: ClassEntityRepository::class)]
#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]
class ClassEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $level = null;

    /**
     * @var Collection<int, StudentEntity>
     */
    #[ORM\OneToMany(targetEntity: StudentEntity::class, mappedBy: 'studentClassName')]
    private Collection $studentOfClass;


    /**
     * @var Collection<int, TeacherEntity>
     */
    #[ORM\OneToMany(targetEntity: TeacherEntity::class, mappedBy: 'nameOfClass')]
    private Collection $listOfTeachers;

    public function __construct()
    {
        $this->studentOfClass = new ArrayCollection();
        $this->listOfTeachers = new ArrayCollection();
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

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, StudentEntity>
     */
    public function getStudentOfClass(): Collection
    {
        return $this->studentOfClass;
    }

    public function addStudentOfClass(StudentEntity $studentOfClass): static
    {
        if (!$this->studentOfClass->contains($studentOfClass)) {
            $this->studentOfClass->add($studentOfClass);
            $studentOfClass->setStudentClassName($this);
        }

        return $this;
    }

    public function removeStudentOfClass(StudentEntity $studentOfClass): static
    {
        if ($this->studentOfClass->removeElement($studentOfClass)) {
            // set the owning side to null (unless already changed)
            if ($studentOfClass->getStudentClassName() === $this) {
                $studentOfClass->setStudentClassName(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, TeacherEntity>
     */
    public function getListOfTeachers(): Collection
    {
        return $this->listOfTeachers;
    }

    public function addListOfTeacher(TeacherEntity $listOfTeacher): static
    {
        if (!$this->listOfTeachers->contains($listOfTeacher)) {
            $this->listOfTeachers->add($listOfTeacher);
            $listOfTeacher->setNameOfClass($this);
        }

        return $this;
    }

    public function removeListOfTeacher(TeacherEntity $listOfTeacher): static
    {
        if ($this->listOfTeachers->removeElement($listOfTeacher)) {
            // set the owning side to null (unless already changed)
            if ($listOfTeacher->getNameOfClass() === $this) {
                $listOfTeacher->setNameOfClass(null);
            }
        }

        return $this;
    }
}
