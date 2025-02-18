<?php

namespace App\Entity;

use App\Repository\AssignmentEntityRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;


#[ORM\Entity(repositoryClass: AssignmentEntityRepository::class)]
#[ApiResource(
    operations:[
        new GetCollection(),
        new Get(),
        new Post(),
        new Put()
    ]
)]

final class AssignmentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $deliverydate = null;

    #[ORM\ManyToOne(inversedBy: 'studentAssignments')]
    private ?SubjectEntity $relatedstudent = null;

    #[ORM\ManyToOne(inversedBy: 'teacherCreatedAssignments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TeacherEntity $teacherWhoAssign = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeliverydate(): ?\DateTimeImmutable
    {
        return $this->deliverydate;
    }

    public function setDeliverydate(\DateTimeImmutable $deliverydate): static
    {
        $this->deliverydate = $deliverydate;

        return $this;
    }

    public function getRelatedstudent(): ?SubjectEntity
    {
        return $this->relatedstudent;
    }

    public function setRelatedstudent(?SubjectEntity $relatedstudent): static
    {
        $this->relatedstudent = $relatedstudent;

        return $this;
    }

    public function getTeacherWhoAssign(): ?TeacherEntity
    {
        return $this->teacherWhoAssign;
    }

    public function setTeacherWhoAssign(?TeacherEntity $teacherWhoAssign): static
    {
        $this->teacherWhoAssign = $teacherWhoAssign;

        return $this;
    }
}
