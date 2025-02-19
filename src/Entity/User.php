<?php
declare(strict_types=1);

namespace App\Entity;


use App\Enums\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    //security:"is_authenticated()",
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
            uriTemplate:"/user/getMe", 
            read : false, #signifie que l'opération ne lira pas directement les données de la base de données ou de la ressource.
            output : [ User::class | ParentEntity::class | StudentEntity::class, TeacherEntity::class], #empêche API Platform de gérer automatiquement la sérialisation de la ressource, car nous souhaitons gérer la réponse avec notre propre logique.
            security:'is_authenticated()',
            controller: UserController::class,
            status: Response::HTTP_OK
        )
    ],
    graphQlOperations: [
        new Query(),
        new Query(
            name:"getMe",
            read:false,
            resolver: UserResolver::class,
            security: 'is_authenticated()',
            args: [],
        ),
        new QueryCollection(
            security:' is_granted("ROLE_ADMIN") '
        ),
        new Mutation(
            name: 'create',  
            security:' is_granted("ROLE_ADMIN") '
        ),
        new DeleteMutation(
            name: 'delete',  
            security:' is_granted("ROLE_ADMIN") '
        ),   
    ]
)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn("discriminator","string")]
#[ORM\DiscriminatorMap([ "user" => "User" ,  "parent"=>"ParentEntity", "teacher"=>"TeacherEntity", "student"=>"StudentEntity"] ) ]
#[ORM\Table(name: '`user`')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue("IDENTITY")]
    #[ORM\Column(type: 'integer')]
    #[Groups("user:read")]
    protected ?int $id = null;

    #[Groups("user:read")]
    #[ORM\Column(length: 255, type:'string')]
    protected ?string $username = null;

    #[Groups("user:read")]
    #[ORM\Column(length: 255, type:'string')]
    protected ?string $name = null;

    #[Groups("user:read")]
    #[ORM\Column(length: 255, type:'string')]
    protected ?string $firstname = null;
    
    #[Groups("user:read")]
    #[Assert\NotNull, Assert\Email()]
    #[ORM\Column(length: 255, unique:true, type:"string")]
    protected ?string $email;
    
    #[Groups("user:read")]
    #[ORM\Column(type: 'json')]
    protected array $roles = [];

    #[Groups("user:read")]
    #[Assert\NotBlank]
    #[ORM\Column(length: 255, type: 'string')]
    protected ?string $password;

    #[Groups("user:read")]
    #[Assert\NotBlank]
    protected ?string $plainPassword = null;
    /**
     * @var Collection<int, AccountValidation>
     */
    #[Groups("user:read")]
    #[ORM\OneToMany(targetEntity: AccountValidation::class, mappedBy: 'relateduservalidation')]
    protected Collection $accountsValidations;

    #[Groups("user:read")]
    #[ORM\Column(nullable: true)]
    protected ?bool $isActive = null;
    
    #[Groups("user:read")]
    protected string $discriminator;

    #[ORM\Column(nullable: true)]
    #[Groups("user:read")]
    protected ?\DateTimeImmutable $lastLogin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("user:read")]
    private ?string $gender = null;

    public function __construct(){
        // $this->isActive = false;
        $this->accountsValidations = new ArrayCollection();
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    
    public function setEmail(String $email): static
    {
        $this->email    = $email;
        $this->username = $email;
        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setPassword( String $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setRoles( array $roles): static {

        $this->roles = array_unique($roles);
        return $this;
    }
    public function getRoles(): array {
        /** @var array $roles */
        $roles = $this->roles;
        $roles =  $roles[] = UserType::USER;
        return array_unique( $this->roles );
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void{
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getUserIdentifier(): string{
        return (string) $this->email;
    }

    
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }


    /**
     * @return Collection<int, AccountValidation>
     */
    public function getAccountsValidations(): Collection
    {
        return $this->accountsValidations;
    }

    public function addAccountsValidation(AccountValidation $accountsValidation): static
    {
        if (!$this->accountsValidations->contains($accountsValidation)) {
            $this->accountsValidations->add($accountsValidation);
            $accountsValidation->setRelateduservalidation($this);
        }

        return $this;
    }

    public function removeAccountsValidation(AccountValidation $accountsValidation): static
    {
        if ($this->accountsValidations->removeElement($accountsValidation)) {
            // set the owning side to null (unless already changed)
            if ($accountsValidation->getRelateduservalidation() === $this) {
                $accountsValidation->setRelateduservalidation(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeImmutable $lastLogin): static
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }
    

}
