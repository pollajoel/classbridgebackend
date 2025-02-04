<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AccountValidationRepository;
use DateTime;
use DateTimeImmutable;

#https://github.com/FriendsOfSymfony/FOSElasticaBundle/blob/master/doc/indexes.md
#[ApiResource(
    operations:[
        new GetCollection(
            uriTemplate: '/accountvalidation/search',
        ),
        new GetCollection()
    ]
)]
#[ORM\Entity(repositoryClass: AccountValidationRepository::class)]
#[ORM\Table(name:'`account_validation`')]
class AccountValidation {
    
    #[ORM\Id]
    #[ORM\GeneratedValue("IDENTITY")]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private DateTime $expiredAt;

    #[ORM\Column(type: 'datetime_immutable',  nullable: true)]
    private ?DateTimeImmutable $activationDate;

    #[ORM\Column(type: 'string', length: 255, nullable:false)]  // S'assurer que la colonne est assez grande
    private string $code;

    #[ORM\ManyToOne(inversedBy: 'accountsValidations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $relateduservalidation = null;

    public function __construct(){
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function getExpiredAt(): DateTime {
        return $this->expiredAt;
    }

    public function setExpiredAt(DateTime $expiredAt): void {
        $this->expiredAt = $expiredAt;
    }

    public function getActivationDate(): ?DateTimeImmutable {
        return $this->activationDate;
    }

    public function setActivationDate(?DateTimeImmutable $activationDate): void {
        $this->activationDate = $activationDate;
    }

    public function getCode(): string {
        return $this->code;
    }

    public function setCode(string $code): void {
        $this->code = $code;
    }


    public function getRelateduservalidation(): ?User
    {
        return $this->relateduservalidation;
    }

    public function setRelateduservalidation(?User $relateduservalidation): static
    {
        $this->relateduservalidation = $relateduservalidation;

        return $this;
    }

}

