<?php

namespace App\Tests;


use App\Service\AccountValidationService;
use Symfony\Component\Messenger\MessageBusInterface;
use Psr\Log\LoggerInterface;
use App\Service\KeycloakService;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\MailerService;
abstract class BaseTestCase extends TestCase
{
    protected $accountValidationServiceMock;
    protected $loggerInterfaceMock;
    protected $messageBusInterfaceMock;
    protected $keycloakServiceMock;
    protected $userServiceMock;
    protected $entityManagerInterfaceMock;
    protected $mailerServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountValidationServiceMock = $this
            ->getMockBuilder(AccountValidationService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->messageBusInterfaceMock = $this->getMockBuilder(MessageBusInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->loggerInterfaceMock     = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->keycloakServiceMock     = $this->getMockBuilder(KeycloakService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->userServiceMock = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManagerInterfaceMock = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mailerServiceMock = $this->getMockBuilder(MailerService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
