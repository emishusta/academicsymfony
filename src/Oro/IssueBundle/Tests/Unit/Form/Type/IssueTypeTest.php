<?php

namespace Oro\IssueBundle\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Form\IssueType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

class IssueTypeTest extends TypeTestCase
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

    protected function getExtensions()
    {
        $mockEntityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRegistry = $this->getMockBuilder('Doctrine\Bundle\DoctrineBundle\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $mockRegistry->expects($this->any())->method('getManagerForClass')
            ->will($this->returnValue($mockEntityManager));

        $mockEntityManager ->expects($this->any())->method('getClassMetadata')
            ->withAnyParameters()
            ->will($this->returnValue(new ClassMetadata('entity')));

        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityManager ->expects($this->any())->method('getRepository')
            ->withAnyParameters()
            ->will($this->returnValue($repository));

        $repository->expects($this->any())->method('findAll')
            ->withAnyParameters()
            ->will($this->returnValue(new ArrayCollection()));


        $entityType = new EntityType($mockRegistry);

        return array(new PreloadedExtension(array(
            'entity' => $entityType,
        ), array()));
    }

    public function testSubmitValidData()
    {
        $formData = array(
            'summary' => 'test_summary',
            'description' => 'test_description',
            'type' => Issue::ISSUE_TYPE_TASK,
            'priority' => Issue::ISSUE_PRIORITY_MINOR,
        );

        $user = $this->getMock('Oro\UserBundle\Entity\User');

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $mockSecurityContext = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockSecurityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $repository = $this->getMockBuilder('Oro\ProjectBundle\Entity\ProjectRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects($this->any())
            ->method('getProjectsByMember')
            ->will($this->returnValue(new ArrayCollection()));
        $type = new IssueType($mockSecurityContext, $repository);
        $form = $this->factory->create($type);

        $issueEntity = new Issue();
        $issueEntity->setSummary($formData['summary'])
            ->setDescription($formData['description'])
            ->setType($formData['type'])
            ->setPriority($formData['priority']);

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($issueEntity, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
