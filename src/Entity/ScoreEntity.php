<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ScoreEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;


#[ORM\Entity(repositoryClass: ScoreEntityRepository::class)]
#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]
#[ORM\Table("Notes")]
class ScoreEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\Column(length: 255)]
    private ?string $appreciation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\ManyToOne(inversedBy: 'studentsScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentEntity $studentEntity = null;

    #[ORM\ManyToOne(inversedBy: 'AssignScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherEntity $relatedTeacher = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getAppreciation(): ?string
    {
        return $this->appreciation;
    }

    public function setAppreciation(string $appreciation): static
    {
        $this->appreciation = $appreciation;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStudentEntity(): ?StudentEntity
    {
        return $this->studentEntity;
    }

    public function setStudentEntity(?StudentEntity $studentEntity): static
    {
        $this->studentEntity = $studentEntity;

        return $this;
    }

    public function getRelatedTeacher(): ?TeacherEntity
    {
        return $this->relatedTeacher;
    }

    public function setRelatedTeacher(?TeacherEntity $relatedTeacher): static
    {
        $this->relatedTeacher = $relatedTeacher;

        return $this;
    }
}
