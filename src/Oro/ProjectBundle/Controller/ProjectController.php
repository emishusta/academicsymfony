<?php

namespace Oro\ProjectBundle\Controller;

use Oro\IssueBundle\OroIssueBundle;
use Oro\ProjectBundle\Entity\Project;
use Oro\ProjectBundle\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/", name="_project")
     * @Template()
     */
    public function indexAction()
    {
        //get all projects for Managers and Admins
         $queryBuilder = $this->getDoctrine()
            ->getRepository('Oro\ProjectBundle\Entity\Project')
            ->createQueryBuilder('p');

        $securityContext = $this->container->get('security.context');
        $isManager = $securityContext->isGranted('ROLE_ADMIN') !== false;
        if (!$isManager) {
            $user = $securityContext->getToken()->getUser();
            $queryBuilder->join('p.members', 'm')
                ->where('m.id = :userId')
                ->setParameter('userId', $user->getId());
        }

        $projects = $queryBuilder->getQuery()->getResult();

        return array('projects' => $projects);
    }

    /**
     * @Route("/create", name="_project_create")
     * @Template()
     */
    public function createAction()
    {
        $project = new Project();
        $form = $this->get('form.factory')->create(new ProjectType(), $project);

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $dbManager = $this->getDoctrine()->getManager();
                $dbManager->persist($project);
                $dbManager->flush();

                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->success('The Project has been saved!');

                return new RedirectResponse($this->generateUrl('_project'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/view/{projectId}", name="_project_view", requirements={"projectId": "\d+"})
     * @Template()
     */
    public function viewAction($projectId)
    {
        $dbManager = $this->getDoctrine()->getManager();
        /** @var Project $project */
        $project = $dbManager->getRepository('Oro\ProjectBundle\Entity\Project')
            ->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $securityContext = $this->container->get('security.context');
        $isManager = $securityContext->isGranted('ROLE_ADMIN') !== false;
        $currentUser = $securityContext->getToken()->getUser();
        if (!$isManager && !$project->getMembers()->contains($currentUser)) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

        $issues = $dbManager->getRepository('OroIssueBundle:Issue')
            ->getIssuesByProject($projectId)->getQuery()->getResult();

        $activities = $dbManager->getRepository('OroIssueBundle:Activity')
            ->getActivitiesByProjectId($projectId)
            ->getQuery()
            ->getResult();

        return array(
            'project' => $project,
            'issues' => $issues,
            'activities' => $activities,
        );
    }

    /**
     * @Route("/update/{projectId}", name="_project_update", requirements={"projectId": "\d+"})
     * @Template()
     */
    public function updateAction($projectId)
    {
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('ROLE_ADMIN') === false) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

        $dbManager = $this->getDoctrine()->getManager();

        /** @var Project $project */
        $project = $dbManager->getRepository('Oro\ProjectBundle\Entity\Project')
            ->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }

        $form = $this->get('form.factory')->create(new ProjectType(), $project);
        $form->add('save', 'submit', array('label' => 'Update'));

        $request = $this->get('request');
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $dbManager->flush();

            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('The Project Data has been saved!');

            return new RedirectResponse($this->generateUrl('_project_view', array('projectId' => $projectId)));
        }

        return array('form' => $form->createView());
    }
}
