<?php

namespace Oro\AppBundle\Controller;

use Oro\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        /** @var User $currentUser */
        $currentUser = $securityContext->getToken()->getUser();
        $dbManager = $this->getDoctrine()->getManager();
        $activities = $dbManager->getRepository('OroIssueBundle:Activity')
            ->getActivitiesByUserMembership($currentUser->getId())
            ->getQuery()
            ->getResult();

        $issues = $dbManager->getRepository('OroIssueBundle:Issue')
            ->getIssuesByUserCollaboration($currentUser->getId())
            ->getQuery()
            ->getResult();

        return array(
            'activities' => $activities,
            'issues' => $issues,
        );
    }
}
