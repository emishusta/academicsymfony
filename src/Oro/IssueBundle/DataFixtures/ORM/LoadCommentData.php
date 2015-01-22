<?php

namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('admin-user');
        $user1 = $this->getReference('manager-user');
        $user2 = $this->getReference('operator-user');
        $user3 = $this->getReference('operator-user1');

        $comment = new Comment();
        $comment->setAuthor($user)
            ->setBody('Please review it and assign to developer')
            ->setIssue($this->getReference('issue-1.1'));

        $manager->persist($comment);

        $comment = new Comment();
        $comment->setAuthor($user1)
            ->setBody('OK')
            ->setIssue($this->getReference('issue-1.1'));

        $manager->persist($comment);

        $comment = new Comment();
        $comment->setAuthor($user3)
            ->setBody('Will fix it ASAP...')
            ->setIssue($this->getReference('issue-2.2'));

        $manager->persist($comment);

        $comment = new Comment();
        $comment->setAuthor($user2)
            ->setBody('Please fix it.')
            ->setIssue($this->getReference('issue-1.1.2'));

        $manager->persist($comment);

        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
