<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ParentEntity;
use App\Entity\StudentEntity;
use App\Entity\TeacherEntity;
use App\Entity\User;
use App\Message\RoleMessage;
use App\Message\UserKeycloakMessage;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use App\Security\User as UserKeyCloak;
use Doctrine\ORM\EntityManagerInterface;
use Elastica\Exception\NotFoundException;

class UserService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private KeycloakService $keycloakService,
        private MessageBusInterface $bus,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function hashPassword(User $user): string
    {

        if ($user == null) {
            throw new \InvalidArgumentException('User cannot be null');
        }

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPlainPassword()
        );
        return $hashedPassword;
    }

    /**
     * Validate role before attaching them to the user
     * UserRoleValidate
     * @param User $user
     */
    public function userRoleValidate(User $user): array
    {
        /**
         * @var array $role
         */
        $rolesOfUser = [];
        if ($user == null) {
            throw new \InvalidArgumentException('User cannot be null');
        }
        $avaibleRoles = $this->keycloakService->getRealmRoles();
        $roles        = array_map(static function ($role) {
            return $role["name"];
        }, $avaibleRoles);
        foreach ($user->getRoles() as $userRole) {
            if (!in_array($userRole, $roles)) {
                throw new \InvalidArgumentException(sprintf('Role "%s" does not exist, Available roles are %s', $userRole, implode(", ", $roles)));
            } else {
                $rolesOfUser[] = $userRole;
            }
        }

        $roles = [];
        foreach ($avaibleRoles as $key => $avaibleRole) {
            if (in_array($avaibleRole["name"], $rolesOfUser)) {
                $roles[] = $avaibleRole;
            }
        }

        return $roles;
    }


    public function addRolesToUser(array $roles, User $user)
    {
        $this->keycloakService->assignRolesToAuser($roles, $user->getUsername());
    }

    /**
     * @param array $roles
     * @param User $user
     *
     */
    public function createUserOnKeycloak(User $user)
    {
        $message = new UserKeycloakMessage(
            $user->getUsername(),
            $user->getEmail(),
            $user->getFirstName(),
            $user->getName(),
            $user->getPassWord(),
            true,
            $user->getIsActive()
        );
        $this->bus->dispatch($message);
    }


    public function userRoleCreateSyncKeycloak(array $roles, User $user)
    {
        $userRoles = new RoleMessage($roles, $user->getUsername());
        $this->bus->dispatch($userRoles);
    }

    public function findOneUserByFilter(array $filter): User
    {
        $user = $this->userRepository->findOneBy($filter);
        if (!$user) {
            throw new UserNotFoundException("User Not Found: " . $filter);
        }
        return $user;
    }


    public function getRepositoryType(array $roles): string
    {

        if (in_array("ROLE_PARENT", $roles)) {
            return ParentEntity::class;
        } else {
            if (in_array("ROLE_TEACHER", $roles)) {
                return TeacherEntity::class;
            } else {
                if (in_array("ROLE_STUDENT", $roles)) {
                    return StudentEntity::class;
                } else {
                    return User::class;
                }
            }
        }
    }


    public function getUserByType(UserKeyCloak $user): object
    {

        $reposType = $this->getRepositoryType($user->getRoles());
        $data      = $this->entityManager->getRepository($reposType)
            ->findOneBy(
                ["email" => $user->getUserIdentifier()]
            );
        return $data;
    }


    public function saveUser(User | ParentEntity | TeacherEntity | StudentEntity $user): object
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
