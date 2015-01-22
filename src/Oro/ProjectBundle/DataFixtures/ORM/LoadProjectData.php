<?php

namespace Oro\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\ProjectBundle\Entity\Project;

class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $project1 = new Project();
        $project1->setLabel('Project Test #1');
        $project1->setSummary("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.");
        $project1->setCode('PT1');
        $project1->addMember($this->getReference('admin-user'));
        $project1->addMember($this->getReference('manager-user'));
        $project1->addMember($this->getReference('operator-user'));

        $manager->persist($project1);

        $project2 = new Project();
        $project2->setLabel('Project Test #2');
        $project2->setSummary("Sed ut perspiciatis, unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam eaque ipsa, quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt, explicabo.");
        $project2->setCode('PT2');
        $project2->addMember($this->getReference('admin-user'));
        $project2->addMember($this->getReference('operator-user1'));

        $manager->persist($project2);

        $manager->flush();

        $this->addReference('project1', $project1);
        $this->addReference('project2', $project2);
    }

    public function getOrder()
    {
        return 2;
    }
}
