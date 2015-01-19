<?php

namespace Oro\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    /**
     * Gets users list that are collaborators for the issue
     *
     * @param $issueId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getIssueCollaborators($issueId)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->leftJoin('u.reporterIssues', 'ri')
            ->leftJoin('u.assignedIssues', 'ai')
            ->leftJoin('u.comments', 'c')
            ->where('ri.id = :issueId')
            ->orWhere('ai.id = :issueId')
            ->orWhere('c.issue = :issueId')
            ->setParameter('issueId', $issueId);

        return $queryBuilder;
    }
}
