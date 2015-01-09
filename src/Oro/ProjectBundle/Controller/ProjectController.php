<?php

namespace Oro\ProjectBundle\Controller;

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
     * @Route("/project", name="_project")
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
     * @Route("/project/new", name="_project_new")
     * @Template()
     */
    public function newAction()
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
     * @Route("/project/view/{projectId}", name="_project_view", requirements={"projectId": "\d+"})
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

        return array('project' => $project);
    }

    /**
     * @Route("/project/edit/{projectId}", name="_project_edit", requirements={"projectId": "\d+"})
     * @Template()
     */
    public function editAction($projectId)
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
