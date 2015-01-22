<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Oro\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername("admin");
        $user->setFullname('Admin Adminov');
        $user->setRole('ROLE_SUPER_ADMIN');
        $user->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword('123123q', $user->getSalt()));
        $user->setEmail("admin@test.com");

        $manager->persist($user);

        $user1 = new User();
        $user1->setUsername("manager");
        $user1->setFullname('Manager Managerov');
        $user1->setRole('ROLE_ADMIN');
        $user1->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user1);
        $user1->setPassword($encoder->encodePassword('qa123123', $user1->getSalt()));
        $user1->setEmail("manager@test.com");

        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername("operator");
        $user2->setFullname('Operator Oper');
        $user2->setRole('ROLE_USER');
        $user2->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user2);
        $user2->setPassword($encoder->encodePassword('qa123123', $user2->getSalt()));
        $user2->setEmail("operator@test.com");

        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername("user");
        $user3->setFullname('Userov User');
        $user3->setRole('ROLE_USER');
        $user3->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user3);
        $user3->setPassword($encoder->encodePassword('qa123123', $user3->getSalt()));
        $user3->setEmail("user@test.com");

        $manager->persist($user3);

        $manager->flush();

        $this->addReference('admin-user', $user);
        $this->addReference('manager-user', $user1);
        $this->addReference('operator-user', $user2);
        $this->addReference('operator-user1', $user3);
    }

    public function getOrder()
    {
        return 1;
    }
}
