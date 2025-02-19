<?php

namespace App\Tests\Controller;

use App\Controller\UserController;
use App\Entity\User;
use App\Tests\BaseTestCase;
use Symfony\Component\HttpFoundation\Response;



class UserControllerTest extends BaseTestCase
{
    
    /**
     * @dataProvider userValidationDataProvider
     */
    public function testValidateUserAccount(bool $valideToken, bool $userAccountIsActive): void
    {
        if ($valideToken) {
            $user = new User();
            if ($userAccountIsActive)
                $user->setIsActive(true);
            else {
                $user->setIsActive(false);
            }
            $user->setRoles(["ROLE_ADMIN"]);
            $this->accountValidationServiceMock
                ->expects($this->once()) // la méthode doit être appelé une seule fois
                ->method('validateAccount')
                ->willReturn($user);
        } else {
            $this->accountValidationServiceMock
                ->expects($this->once()) // la méthode doit être appelé une seule fois
                ->method('validateAccount')
                ->willReturn(null);
        }

        $userController  =  new UserController($this->accountValidationServiceMock, $this->messageBusInterfaceMock, $this->loggerInterfaceMock, $this->keycloakServiceMock);
        /** 
         * @var Response $response
         */
        $response = $userController->validateUserAccount($this->userServiceMock, "token");
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
    public static function userValidationDataProvider()
    {
        return [
            // valideToken, userAccountIsActive
            [true, true],
            [true, false],
            [false, false],
        ];
    }




}
