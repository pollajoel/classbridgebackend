<?php
namespace App\Tests\Entity;
use App\Entity\TeacherEntity;
use PHPUnit\Framework\TestCase;

class TeacherEntityTest extends TestCase{

    /**
     * @dataProvider teacherDataProvider
     */
    public function testTeacherEntity(
        string $name,
        string $email,
        string $username,
        string $password,
        array $roles,
        string $expectedRole
    ): void
    {
        $teacher = new TeacherEntity();
        $teacher->setName($name);
        $teacher->setPassword($password);
        $teacher->setEmail($email);
        $this->assertEquals($email, $teacher->getUsername());
        $this->assertEquals($name, $teacher->getName());
        $this->assertEquals($email, $teacher->getEmail());
        $this->assertEquals($email, $teacher->getUserIdentifier());
        $this->assertEquals($password, $teacher->getPassword());
        $teacher->setRoles($roles);
        $this->assertContainsOnlyString($roles);
        $this->assertIsArray($roles);
        $this->assertTrue(in_array($expectedRole, $roles));
    }


    public static function teacherDataProvider(){
        return [
            ['John Doe', 'john.doe@example.com', 'johndoe', 'password', ["ROLE_ADMIN"], "ROLE_ADMIN"],
            ['Jane Doe', 'jane.doe@example.com', 'janedoe', 'password', ["ROLE_USER"],  "ROLE_USER" ],
        ];
    }

}


