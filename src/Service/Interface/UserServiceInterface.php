<?php

declare(strict_types=1);

namespace App\Service\Interface;

use App\Entity\ParentEntity;
use App\Entity\StudentEntity;
use App\Entity\TeacherEntity;
use App\Entity\User;
use App\Security\User as UserKeyCloak;


interface UserServiceInterface
{
    public function hashPassword(User $user): string;

    /**
     * Validate role before attaching them to the user
     * UserRoleValidate
     * @param User $user
     */
    public function userRoleValidate(User $user): array;
    public function addRolesToUser(array $roles, User $user): void;
    /**
     * @param array $roles
     * @param User $user
     *
     */
    public function createUserOnKeycloak(User $user): void;
    public function userRoleCreateSyncKeycloak(array $roles, User $user): void;
    public function findOneUserByFilter(array $filter): User;
    public function getRepositoryType(array $roles): string;
    public function getUserByType(UserKeyCloak $user): object;
    public function saveUser(User | ParentEntity | TeacherEntity | StudentEntity $user): object;
}
