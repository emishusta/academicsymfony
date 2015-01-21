<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ActivityRepository extends EntityRepository
{
    /**
     * Gets project activities list
     *
     * @param $projectId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getActivitiesByProjectId($projectId)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->join('a.issue', 'i')
            ->join('i.project', 'p')
            ->where('p.id = :projectId')
            ->orderBy('a.created', 'DESC')
            ->setParameter('projectId', $projectId);

        return $queryBuilder;
    }
}