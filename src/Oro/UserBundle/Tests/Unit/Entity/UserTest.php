<?php

namespace Oro\UserBundle\Tests\Unit\Entity;

use Oro\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $property
     * @param string $value
     * @param string $expected
     * @dataProvider getSetDataProvider
     */
    public function testGetSet($property, $value, $expected)
    {
        $obj = new User();
        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($expected, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }
    /**
     * Data provider
     *
     * @return array
     */
    public function getSetDataProvider()
    {
        $avatarFile = new UploadedFile(__DIR__ . '/../../Fixtures/test_avatar.jpg', 'test_avatar.jpg');
        $createdDate = new \DateTime();
        $issueCollaborator = $this->getMock('Oro\IssueBundle\Entity\Issue');

        return array(
            'email'    => array('email', 'test@test.com', 'test@test.com'),
            'username' => array('username', 'test_name', 'test_name'),
            'fullname' => array('fullname', 'Test Testov', 'Test Testov'),
            'avatar'   => array('avatarPath', 'filepath', 'filepath'),
            'avatarFile'   => array('avatarFile', $avatarFile, $avatarFile),
            'password' => array('password', 'qa123123', 'qa123123'),
            'role'     => array('role', 'manager', 'manager'),
            'created'     => array('created', $createdDate, $createdDate),
            'salt'     => array('salt', 'test_salt', 'test_salt'),
            'timezone'     => array('timezone', 'Europe/Kiev', 'Europe/Kiev'),
            'issueCollaborator'     => array('issueCollaborator', $issueCollaborator, $issueCollaborator),
        );
    }
}
