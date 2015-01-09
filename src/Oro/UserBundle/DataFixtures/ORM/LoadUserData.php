<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Oro\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
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

        $user = new User();
        $user->setUsername("manager");
        $user->setFullname('Manager Managerov');
        $user->setRole('ROLE_ADMIN');
        $user->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword('qa123123', $user->getSalt()));
        $user->setEmail("manager@test.com");

        $manager->persist($user);

        $user = new User();
        $user->setUsername("operator");
        $user->setFullname('Operator Oper');
        $user->setRole('ROLE_USER');
        $user->setTimezone('Europe/Kiev');
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword('qa123123', $user->getSalt()));
        $user->setEmail("operator@test.com");

        $manager->persist($user);

        $manager->flush();
    }
}
