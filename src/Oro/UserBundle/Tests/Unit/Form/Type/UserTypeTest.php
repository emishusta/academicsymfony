<?php

namespace Oro\UserBundle\Tests\Unit\Form\Type;

use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\ConstraintViolationList;

class UserTypeTest extends TypeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $validator = $this->getMock('\Symfony\Component\Validator\ValidatorInterface');
        $validator->method('validate')->will($this->returnValue(new ConstraintViolationList()));

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new FormTypeValidatorExtension(
                    $validator
                )
            )
            ->addTypeGuesser(
                $this->getMockBuilder(
                    'Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser'
                )
                    ->disableOriginalConstructor()
                    ->getMock()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    public function testSubmitValidData()
    {
        $avatarFile = new UploadedFile(__DIR__ . '/../../../Fixtures/test_avatar.jpg', 'test_avatar.jpg');

        $formData = array(
            'username' => 'test_username',
            'email' => 'test_username@test.com',
            'fullname' => 'test_fullname',
            'password' => array(
                'first' => 'qa123123',
                'second' => 'qa123123'
            ),
            'role' => 'ROLE_USER',
            'timezone' => 'Europe/Kiev',
            'avatar_file' => $avatarFile,
        );

        $type = new UserType();
        $form = $this->factory->create($type);

        $userEntity = new User();
        $userEntity->setUsername($formData['username'])
            ->setEmail($formData['email'])
            ->setFullname($formData['fullname'])
            ->setPassword($formData['password']['first'])
            ->setRole($formData['role'])
            ->setTimezone($formData['timezone'])
            ->setAvatarFile($formData['avatar_file']);

        // submit the data to the form directly
        $form->submit($formData);

        //set salt from formData because it is random generated
        $userEntity->setSalt($form->getData()->getSalt());

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($userEntity, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
