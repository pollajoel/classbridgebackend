<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\DeleteMutation;
use ApiPlatform\Metadata\GraphQl\Mutation;
use ApiPlatform\Metadata\GraphQl\Query;
use ApiPlatform\Metadata\GraphQl\QueryCollection;
use ApiPlatform\Metadata\Put;
use App\Controller\UserController;
use App\Resolver\UserResolver;
use App\State\UserProcessorPost;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties:["discriminator" => "teacher"])]
#[ApiResource(
    security:"is_authenticated()",
    normalizationContext: ['groups' => ['user:read']],
    securityMessage: 'You are not authenticated',
    //denormalizationContext: ['groups' => ['user', 'user:write']],
    # Restfull api operations
    operations:[
        new GetCollection(
            security: 'is_granted("ROLE_ADMIN")',
        ),
        new Post(
            processor: UserProcessorPost::class,
            security : 'is_granted("ROLE_ADMIN")',
            securityMessage: 'Only admins can create new user.'
        ),
        new Put(
            security: 'is_granted("ROLE_ADMIN", object) or is_granted("ROLE_TEACHER)',
            securityMessage: 'You are not allow to to update user'
        ),
        new Patch(),
        new Get(),
        new Get(
            read : false, #signifie que l'opération ne lira pas directement les données de la base de données ou de la ressource.
            //output : false, #empêche API Platform de gérer automatiquement la sérialisation de la ressource, car nous souhaitons gérer la réponse avec notre propre logique.
            security:'is_authenticated()',
            controller: UserController::class
        )
    ],
    graphQlOperations: [
        new Query(),
        new QueryCollection(),
        new Mutation(name: 'create'),
        new DeleteMutation(name: 'delete'),   
    ]
)]
#[ORM\Entity()]
final class TeacherEntity extends User
{
    /**
     * @var Collection<int, ScoreEntity>
     */
    #[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: ScoreEntity::class, mappedBy: 'relatedTeacher')]
    private Collection $AssignScores;

    /**
     * @var Collection<int, AssignmentEntity>
     */
    #[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: AssignmentEntity::class, mappedBy: 'teacherWhoAssign')]
    private Collection $teacherCreatedAssignments;

    /**
     * @var Collection<int, NonAttendanceEntity>
     */
    #[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: NonAttendanceEntity::class, mappedBy: 'nonAttendanceTeacher')]
    private Collection $teacherCreatedNonAttendances;

    #[Groups("user:read")]
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

