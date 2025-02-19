<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Service\Interface\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Service\UserService;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class UserControllerWebTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client     = static::createClient();
        $container  = static::getContainer();
        $userData = ['realm_access' => ['roles' => ['ROLE_USER'],], 'preferred_username' => 'test@example.com', 'email' => 'test@example.com'];
        // $userMock = $this
        // // ->getMockBuilder(UserInterface::class)
        // // ->disableOriginalConstructor()
        // // ->getMock(); 
        // // $userMock->method('getRoles')->willReturn(['ROLE_USER']);
        // // $userMock->method('getUserIdentifier')->willReturn('test@example.com');

        // Création d'un mock du service JWT
         $jwtManagerMock = $this->createMock(JWTTokenManagerInterface::class);
         $jwtManagerMock->method('create')->willReturn('mocked-jwt-token');
         $jwtManagerMock->method('parse')->willReturn($userData);
        // Ajout du mock dans le conteneur de services
         $container->set(JWTTokenManagerInterface::class, $jwtManagerMock);
        //Mocker le userService
        $user = (new User())
                ->setEmail('test@example.com')
                ->setRoles(['ROLE_USER'])
                ->setEmail('test@example.com')
                ->setId(1);
        

        $userServiceMock = $userServiceMock = $this->createMock(UserService::class);
        $userServiceMock->method('getUserByType')->willReturn($user);
        // Remplacer le service réel par le mock dans le container Symfony
        $container->set(UserService::class, $userServiceMock);
        
    
        // Ajout du token dans la requête
        $client->request('GET', '/api/user/getMe', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer mocked-jwt-token',
        ]);
        $this->assertResponseIsSuccessful();
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('email', $response);
        $this->assertArrayHasKey('roles', $response);
        $this->assertEquals($response['email'], 'test@example.com' );

    }
}
