<?php

namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\Activity;
use Oro\IssueBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $project1 = $this->getReference('project1');
        $project2 = $this->getReference('project2');
        $user = $this->getReference('admin-user');
        $user1 = $this->getReference('manager-user');
        $user2 = $this->getReference('operator-user');
        $user3 = $this->getReference('operator-user1');

        $issueStory = new Issue();
        $issueStory->setProject($project1);
        $issueStory->setSummary('Test Issue #1.1');
        $issueStory->setDescription('Nemo enim ipsam voluptatem, quia voluptas sit, aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos, qui ratione voluptatem sequi nesciunt, neque porro quisquam est, qui dolorem ipsum, quia dolor sit, amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt, ut labore et dolore magnam aliquam quaerat voluptatem.');
        $issueStory->setType(Issue::ISSUE_TYPE_STORY);
        $issueStory->setPriority(Issue::ISSUE_PRIORITY_MAJOR);
        $issueStory->setAssignee($user1);
        $issueStory->setReporter($user);
        $issueStory->addCollaborator($user1);
        $issueStory->addCollaborator($user);

        $manager->persist($issueStory);

        $activity = new Activity();
        $activity->setType(Activity::TYPE_NEW_ISSUE)
            ->setIssueNewStatus($issueStory->getStatus())
            ->setUser($user)
            ->setIssue($issueStory);

        $manager->persist($activity);

        $issue111 = new Issue();
        $issue111->setProject($project1);
        $issue111->setSummary('Test Issue #1.1.1');
        $issue111->setDescription('Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?');
        $issue111->setType(Issue::ISSUE_TYPE_SUBTASK);
        $issue111->setParent($issueStory);
        $issue111->setPriority(Issue::ISSUE_PRIORITY_CRITICAL);
        $issue111->setAssignee($user2);
        $issue111->setReporter($user);
        $issue111->addCollaborator($user2);
        $issue111->addCollaborator($user);
        $issue111->setStatus(Issue::ISSUE_STATUS_IN_PROGRESS);

        $manager->persist($issue111);

        $activity = new Activity();
        $activity->setType(Activity::TYPE_NEW_ISSUE)
            ->setIssueNewStatus(Issue::ISSUE_STATUS_OPEN)
            ->setUser($user)
            ->setIssue($issue111);

        $manager->persist($activity);

        $issue112 = new Issue();
        $issue112->setProject($project1);
        $issue112->setSummary('Test Issue #1.1.2');
        $issue112->setDescription('Nisi ut aliquid ex ea commodi consequatur!');
        $issue112->setType(Issue::ISSUE_TYPE_SUBTASK);
        $issue112->setParent($issueStory);
        $issue112->setPriority(Issue::ISSUE_PRIORITY_MAJOR);
        $issue112->setAssignee($user2);
        $issue112->setReporter($user1);
        $issue112->addCollaborator($user1);
        $issue112->addCollaborator($user2);

        $manager->persist($issue112);

        $activity = new Activity();
        $activity->setType(Activity::TYPE_NEW_ISSUE)
            ->setIssueNewStatus($issue112->getStatus())
            ->setUser($user1)
            ->setIssue($issue112);

        $manager->persist($activity);

        $issue21 = new Issue();
        $issue21->setProject($project2)
            ->setSummary('Test Issue #2.1')
            ->setDescription('Quis autem vel eum iure reprehenderit, qui in ea voluptate velit esse, quam nihil molestiae consequatur, vel illum, qui dolorem eum fugiat, quo voluptas nulla pariatur?')
            ->setType(Issue::ISSUE_TYPE_TASK)
            ->setPriority(Issue::ISSUE_PRIORITY_MINOR)
            ->setAssignee($user3)
            ->setReporter($user)
            ->addCollaborator($user)
            ->addCollaborator($user3);

        $manager->persist($issue21);

        $activity = new Activity();
        $activity->setType(Activity::TYPE_NEW_ISSUE)
            ->setIssueNewStatus($issue21->getStatus())
            ->setUser($user)
            ->setIssue($issue21);

        $manager->persist($activity);

        $issue22 = new Issue();
        $issue22->setProject($project2)
            ->setSummary('Test Issue #2.2')
            ->setDescription('Quam nihil molestiae consequatur, vel illum, qui dolorem eum fugiat, quo voluptas nulla pariatur? Quis autem vel eum iure reprehenderit, qui in ea voluptate velit esse.')
            ->setType(Issue::ISSUE_TYPE_BUG)
            ->setPriority(Issue::ISSUE_PRIORITY_BLOCKER)
            ->setAssignee($user3)
            ->setReporter($user3)
            ->addCollaborator($user3);

        $manager->persist($issue22);

        $activity = new Activity();
        $activity->setType(Activity::TYPE_NEW_ISSUE)
            ->setIssueNewStatus($issue22->getStatus())
            ->setUser($user3)
            ->setIssue($issue22);

        $manager->persist($activity);

        $manager->flush();

        $this->addReference('issue-1.1', $issueStory);
        $this->addReference('issue-1.1.1', $issue111);
        $this->addReference('issue-1.1.2', $issue112);
        $this->addReference('issue-2.1', $issue21);
        $this->addReference('issue-2.2', $issue21);
    }

    public function getOrder()
    {
        return 3;
    }
}
