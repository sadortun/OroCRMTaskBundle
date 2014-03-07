<?php

namespace OroCRM\Bundle\TaskBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function getTaskAssignedTo($userId, $limit)
    {
        $queryBuilder = $this->createQueryBuilder('task');
        return $queryBuilder->where("task.assignedTo = :assignedTo")
            ->orderBy('task.dueDate', 'asc')
            ->addOrderBy('task.workflowStep', 'asc')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->setParameters(array('assignedTo' => $userId))
            ->getQuery()
            ->execute();
    }
}
