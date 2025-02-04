<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Message\RoleMessage;
use App\Message\UserKeycloakMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private KeycloakService $keycloakService,
        private MessageBusInterface $bus
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
        $roles        = array_map( static function ($role) {
            return $role["name"];
        }, $avaibleRoles);
        foreach ($user->getRoles() as $userRole) {
            if (!in_array( $userRole, $roles)) {
                throw new \InvalidArgumentException(sprintf('Role "%s" does not exist, Available roles are %s', $userRole, implode(", ",$roles)));
            }else{
                $rolesOfUser[] = $userRole;
            }
        }

      $roles = [];
      foreach( $avaibleRoles as $key => $avaibleRole){
        if( in_array( $avaibleRole["name"], $rolesOfUser) ) 
        {
          $roles[] = $avaibleRole;
        }
      }
 
     return $roles;
    }


    public function addRolesToUser( array $roles, User $user ){
        $this->keycloakService->assignRolesToAuser($roles, $user->getUsername());
    }

    /**
     * @param array $roles
     * @param User $user
     *
     */
    public function createUserOnKeycloak( User $user){
        $message = new UserKeycloakMessage(
            $user->getUsername(), 
            $user->getEmail(), 
            $user->getFirstName(), 
            $user->getName(), 
            $user->getPassWord(), 
            true, 
            $user->getIsActive());
        $this->bus->dispatch($message);
    }


    public function userRoleCreateSyncKeycloak(array $roles, User $user){
        $userRoles = new RoleMessage($roles, $user->getUsername());
        $this->bus->dispatch($userRoles);
    }
}
