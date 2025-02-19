<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\TestCase;
use App\Controller\UserController;
use App\Entity\User;
use App\Service\AccountValidationService;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Service\KeycloakService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Response;



class UserControllerTest extends TestCase
{

    /**
     * @dataProvider dataProvider
     */
    public function testValidateUserAccount(bool $valideToken, bool $userAccountIsActive): void
    {

        $accountValidationServiceMock = $this
            ->getMockBuilder(AccountValidationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $messageBusInterfaceMock = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $loggerInterfaceMock     = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $keycloakServiceMock     = $this->getMockBuilder(KeycloakService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();


        if ($valideToken) {
            $user = new User();
            if ($userAccountIsActive)
                $user->setIsActive(true);
            else {
                $user->setIsActive(false);
            }
            $user->setRoles(["ROLE_ADMIN"]);
            $accountValidationServiceMock
                ->expects($this->once()) // la méthode doit être appelé une seule fois
                ->method('validateAccount')
                ->willReturn($user);
        } else {
            $accountValidationServiceMock
                ->expects($this->once()) // la méthode doit être appelé une seule fois
                ->method('validateAccount')
                ->willReturn(null);
        }

        $userController  =  new UserController($accountValidationServiceMock, $messageBusInterfaceMock, $loggerInterfaceMock, $keycloakServiceMock);
        /** 
         * @var Response $response
         */
        $response = $userController->validateUserAccount($userServiceMock, "token");
        if( !$valideToken ){
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertEquals('code de validation invalide', $response->getContent());
        }else{
             if( $userAccountIsActive ){
                $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
                $this->assertEquals('Votre compte est Activé et validé', $response->getContent());
             }else{
                 $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
                 $this->assertEquals('Code de validation expiré', $response->getContent());
             }
        }
        
    }
    public static function dataProvider()
    {
        return [
            // valideToken, userAccountIsActive
            [true, true],
            [true, false],
            [false, false],
        ];
    }
}
