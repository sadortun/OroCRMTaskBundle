<?php

namespace OroCRM\Bundle\TaskBundle\Provider;

use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\ActivityListBundle\Entity\Manager\CollectListManager;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ActivityListBundle\Model\ActivityListProviderInterface;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityConfigBundle\Config\Id\ConfigIdInterface;
use OroCRM\Bundle\TaskBundle\Entity\Task;

class TaskActivityListProvider implements ActivityListProviderInterface
{
    const ACTIVITY_CLASS = 'OroCRM\Bundle\TaskBundle\Entity\Task';

    /** @var DoctrineHelper */
    protected $doctrineHelper;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicableTarget(ConfigIdInterface $configId, ConfigManager $configManager)
    {
        $provider = $configManager->getProvider('activity');
        return $provider->hasConfigById($configId) && $provider->getConfigById($configId)->has('activities');
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject($entity)
    {
        /** @var $entity Task */
        return $entity->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(ActivityList $activityList, $activityEntity)
    {
        /** @var Task $activityEntity */
        if ($activityList->getVerb() === CollectListManager::STATE_CREATE) {
            $activityList->setOwner($activityEntity->getOwner());
        } else {
            $activityList->setEditor($activityEntity->getOwner());
        }
        $activityList->setData(
            [
                'description'        => $activityEntity->getDescription(),
                'due_date'           => $activityEntity->getDueDate()->format('c'),
                'task_priority_name' => $activityEntity->getTaskPriority()->getLabel(),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForView(ActivityList $activityEntity)
    {
        return $activityEntity->getData();
    }

    /**
     * {@inheritdoc
     */
    public function getTemplate()
    {
        return 'OroCRMTaskBundle:Task:js/activityItemTemplate.js.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutes()
    {
        return [
            'itemView'   => 'orocrm_task_widget_info',
            'itemEdit'   => 'orocrm_task_update',
            'itemDelete' => 'oro_api_delete_task'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getActivityClass()
    {
        return self::ACTIVITY_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    public function getActivityId($entity)
    {
        return $this->doctrineHelper->getSingleEntityIdentifier($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable($entity)
    {
        if (is_object($entity)) {
            $entity = $this->doctrineHelper->getEntityClass($entity);
        }

        return $entity == self::ACTIVITY_CLASS;
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetEntities($entity)
    {
        return $entity->getActivityTargetEntities();
    }
}
