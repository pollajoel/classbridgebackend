<?php
namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\QueryItemResolverInterface;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\User;
use App\Entity\ParentEntity;
use App\Entity\TeacherEntity;
use App\Entity\StudentEntity;
final class UserResolver implements QueryItemResolverInterface {


    public function __construct(private TokenStorageInterface $tokenStorage, private readonly UserService $userService)
    {}

    /**
     * @param User| ParentEntity | TeacherEntity | StudentEntity  $item
     *
     * @return  User | ParentEntity | TeacherEntity | StudentEntity
     */
    public function __invoke(?object $item, array $context):  object
    {
        // Query arguments are in $context['args'].

        // Do something with the user.
        // Or fetch the book if it has not been retrieved.
        
        $user = $this->tokenStorage->getToken()?->getUser();
        if( $user ){
            $data = $this->userService->getUserByType($user);
        }    
        return $data;
    }
}