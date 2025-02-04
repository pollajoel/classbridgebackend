<?php
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
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
class ParentEntity extends User
{
    /**
     * @var Collection<int, StudentEntity>
     */
    #[ORM\OneToMany(targetEntity: StudentEntity::class, mappedBy: 'parentofStudent')]
    private Collection $folowStudents;

    /**
     * @var Collection<int, MessageEntity>
     */
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