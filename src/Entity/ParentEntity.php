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
use App\Controller\ParentController;
use App\Controller\UserController;
use App\Resolver\UserResolver;
use App\State\UserProcessorPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiFilter(SearchFilter::class, properties:["discriminator" => "parent"])]
#[ApiResource(
    security:"is_authenticated()",
    //normalizationContext: ['groups' => ['user:read']],
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
            securityMessage: 'Only admins can create new user.',
            status: Response::HTTP_CREATED
        ),
        new Put(
            security: 'is_granted("ROLE_ADMIN", object) or is_granted("ROLE_PARENT)',
            securityMessage: 'You are not allow to to update user',
            status: Response::HTTP_OK
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
class ParentEntity extends User
{
    /**
     * @var Collection<int, StudentEntity>
     */
    //#[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: StudentEntity::class, mappedBy: 'parentofStudent')]
    private Collection $folowStudents;

    /**
     * @var Collection<int, MessageEntity>
     */
    //#[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: MessageEntity::class, mappedBy: 'parentHandleMessage')]
    private Collection $exchangeMessages;

    public function __construct()
    {
        parent::__construct();
        $this->folowStudents = new ArrayCollection();
        $this->exchangeMessages = new ArrayCollection();
    }

    /**
     * @return Collection<int, StudentEntity>
     */
    public function getFolowStudents(): Collection
    {
        return $this->folowStudents;
    }

    public function addFolowStudent(StudentEntity $folowStudent): static
    {
        if (!$this->folowStudents->contains($folowStudent)) {
            $this->folowStudents->add($folowStudent);
            $folowStudent->setParentofStudent($this);
        }

        return $this;
    }

    public function removeFolowStudent(StudentEntity $folowStudent): static
    {
        if ($this->folowStudents->removeElement($folowStudent)) {
            // set the owning side to null (unless already changed)
            if ($folowStudent->getParentofStudent() === $this) {
                $folowStudent->setParentofStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageEntity>
     */
    public function getExchangeMessages(): Collection
    {
        return $this->exchangeMessages;
    }

    public function addExchangeMessage(MessageEntity $exchangeMessage): static
    {
        if (!$this->exchangeMessages->contains($exchangeMessage)) {
            $this->exchangeMessages->add($exchangeMessage);
            $exchangeMessage->setParentHandleMessage($this);
        }

        return $this;
    }

    public function removeExchangeMessage(MessageEntity $exchangeMessage): static
    {
        if ($this->exchangeMessages->removeElement($exchangeMessage)) {
            // set the owning side to null (unless already changed)
            if ($exchangeMessage->getParentHandleMessage() === $this) {
                $exchangeMessage->setParentHandleMessage(null);
            }
        }

        return $this;
    }
}