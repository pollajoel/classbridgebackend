<?php
declare(strict_types=1);
namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTimeImmutable;
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
use App\State\UserProcessorPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties:["discriminator" => "studentr"])]
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
            controller: UserController::class,
            status: Response::HTTP_OK
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
class StudentEntity extends User {
    private DateTimeImmutable $bornDate;

    /**
     * @var Collection<int, ScoreEntity>
     */
    #[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: ScoreEntity::class, mappedBy: 'studentEntity')]
    private Collection $studentsScores;

    #[Groups("user:read")]
    #[ORM\ManyToOne(inversedBy: 'folowStudents')]
    private ?ParentEntity $parentofStudent = null;

    #[Groups("user:read")]
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