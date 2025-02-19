<?php

declare(strict_types=1);
namespace App\Tests\Service;

use App\Entity\AccountValidation;
use App\Service\AccountValidationService;
use App\Tests\BaseTestCase;
use App\Repository\AccountValidationRepository;
use ReflectionClass;

class AccountValidationServiceTest extends BaseTestCase{


    /**
     * @dataProvider findAccountValidationByCodeData
     */
    public function testfindAccountValidationByCode(AccountValidation | null  $account, string $code ): void {

        $accountValidationRepositoryMock = $this->createMock(AccountValidationRepository::class);
        $accountValidationRepositoryMock->method('findOneBy')->willReturn($account);
        $this->entityManagerInterfaceMock->method('getRepository')->willReturn($accountValidationRepositoryMock);
        $accountValidationService = new AccountValidationService($this->entityManagerInterfaceMock, $this->loggerInterfaceMock, $this->mailerServiceMock);
        if( !$account ) {
            $this->expectExceptionMessage('Validation code not found');
        }

        /** 
         * @var AccountValidation $accountValidation
         */
        $accountValidation = $accountValidationService->findAccountValidationByCode($code);
        $this->assertEquals($code, $accountValidation->getCode());
        $this->assertEquals($account, $accountValidation);   

        $refClass = new ReflectionClass($accountValidation);
        $properties = $refClass->getProperties(); // Récupère toutes les propriétés de la classe
    }


    public static function findAccountValidationByCodeData() {
        $account = new AccountValidation();
        $account->setCode('ValidationCode#__0987654321');
        return [
            [ $account, 'ValidationCode#__0987654321'],
            [ null ,    'ValidationCode#__0987654321']
        ];
    }

}