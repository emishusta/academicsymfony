<?php

namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Oro\IssueBundle\Entity\Activity;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivityNewListener
{
    protected $container;

    protected $mailerFrom;

    /**
     * @param ContainerInterface $container
     * @param $mailerFrom
     */
    public function __construct(ContainerInterface $container, $mailerFrom)
    {
        $this->container = $container;
        $this->mailerFrom = $mailerFrom;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Activity) {

            $message = $this->container->get('mailer')->createMessage()
                ->setSubject($this->container->get('translator')->trans('New Activity Notification'))
                ->setFrom($this->mailerFrom);

            foreach ($entity->getIssue()->getCollaborators() as $collaborator) {
                $body = $this->container->get('templating')
                    ->render(
                        'OroIssueBundle:Activity:list.html.twig',
                        array('activities' => array($entity), 'user' => $collaborator)
                    );
                $message->setTo(array($collaborator->getEmail() => $collaborator->getFullname()))
                    ->setBody($body, 'text/html');
                $this->container->get('mailer')->send($message);
            }
        }
    }
}
