<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\NonAttendanceEntityRepository;
use Doctrine\ORM\Mapping as ORM;
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
#[ORM\Entity(repositoryClass: NonAttendanceEntityRepository::class)]
class NonAttendanceEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(nullable: true)]
    private ?bool $justified = null;

    #[ORM\ManyToOne(inversedBy: 'studentNonattendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubjectEntity $relatedStudent = null;

    #[ORM\ManyToOne(inversedBy: 'teacherCreatedNonAttendances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherEntity $nonAttendanceTeacher = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function isJustified(): ?bool
    {
        return $this->justified;
    }

    public function setJustified(?bool $justified): static
    {
        $this->justified = $justified;

        return $this;
    }

    public function getRelatedStudent(): ?SubjectEntity
    {
        return $this->relatedStudent;
    }

    public function setRelatedStudent(?SubjectEntity $relatedStudent): static
    {
        $this->relatedStudent = $relatedStudent;

        return $this;
    }

    public function getNonAttendanceTeacher(): ?TeacherEntity
    {
        return $this->nonAttendanceTeacher;
    }

    public function setNonAttendanceTeacher(?TeacherEntity $nonAttendanceTeacher): static
    {
        $this->nonAttendanceTeacher = $nonAttendanceTeacher;

        return $this;
    }
}
