<?php

namespace Oro\ProjectBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProjectRepository extends EntityRepository
{
    /**
     * Gets all projects where the user is the member
     *
     * @param null $userId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProjectsByMember($userId = null)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        if ($userId !== null) {
            $queryBuilder->join('p.members', 'm')
                ->where('m.id=:memberId')
                ->setParameter('memberId', $userId);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
