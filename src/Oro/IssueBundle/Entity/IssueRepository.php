<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

class IssueRepository extends EntityRepository
{
    /**
     * Gets issues filtered by project ID
     *
     * @param $projectId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getIssuesByProject($projectId)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->join('i.project', 'p')
            ->where('p.id = :projectId')
            ->setParameter('projectId', $projectId);

        return $queryBuilder;
    }

    /**
     * Gets issue that has STORY type only
     *
     * @param null $projectId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getStoriesByProject($projectId = null)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->where('i.type = :type')
            ->setParameter('type', 'STORY');
        if ($projectId !== null) {
            $queryBuilder->join('i.project', 'p')
                ->andWhere('p.id=:projectId')
                ->setParameter('projectId', $projectId);
        }

        return $queryBuilder;
    }

    /**
     * Gets issues where user is member of the issue project
     *
     * @param null $userId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getIssuesByUserId($userId = null)
    {
        $queryBuilder = $this->createQueryBuilder('i');

        if ($userId !== null) {
            $queryBuilder->join('i.project', 'p')
                ->join('p.members', 'm')
                ->where('m.id = :userId')
                ->setParameter('userId', $userId);
        }

        return $queryBuilder;
    }

    /**
     * Gets issues that has assignee the user ID
     *
     * @param $userId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getIssuesAssignedToUser($userId)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->join('i.assignee', 'a')
            ->where('a.id = :userId')
            ->setParameter('userId', $userId);

        return $queryBuilder;
    }
}
