<?php

namespace Oro\IssueBundle\Controller;

use Oro\IssueBundle\Entity\Activity;
use Oro\IssueBundle\Entity\Comment;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Form\CommentType;
use Oro\IssueBundle\Form\IssueEditType;
use Oro\IssueBundle\Form\IssueType;
use Oro\ProjectBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class IssueController extends Controller
{
    /**
     * @Route("/", name="_issue")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        $currentUser = $securityContext->getToken()->getUser();
        $userId = $securityContext->isGranted('ROLE_ADMIN') ? null : $currentUser->getId();
        $queryBuilder = $this->getDoctrine()
            ->getRepository('Oro\IssueBundle\Entity\Issue')
            ->getIssuesByUserId($userId);

        $issues = $queryBuilder->getQuery()->getResult();

        return array('issues' => $issues);
    }

    /**
     * @Route("/create/project/{projectId}",
     *  name="_issue_create",
     *  defaults={"projectId": null},
     *  requirements={"projectId": "\d+"})
     * @Template()
     */
    public function createAction($projectId)
    {
        $securityContext = $this->container->get('security.context');
        $currentUser = $securityContext->getToken()->getUser();

        $issue = new Issue();
        $dbManager = $this->getDoctrine()->getManager();
        if (!empty($projectId)) {
            $project = $dbManager->getRepository('Oro\ProjectBundle\Entity\Project')->find($projectId);
            $issue->setProject($project);
        }
        $projectRepository = $dbManager->getRepository('Oro\ProjectBundle\Entity\Project');
        $form = $this->get('form.factory')->create(new IssueType($securityContext, $projectRepository), $issue);

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $issue->setReporter($currentUser);
                $issue->addCollaborator($currentUser);
                if (!$issue->getCollaborators()->contains($issue->getAssignee())) {
                    $issue->addCollaborator($issue->getAssignee());
                }

                $dbManager->persist($issue);

                $activity = new Activity();
                $activity->setType(Activity::TYPE_NEW_ISSUE)
                    ->setIssueNewStatus($issue->getStatus())
                    ->setUser($currentUser);
                $activity->setIssue($issue);
                $dbManager->persist($activity);

                $dbManager->flush();

                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->success('The Issue has been saved!');

                return new RedirectResponse($this->getRequest()->headers->get('referer'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/view/{issueId}", name="_issue_view", requirements={"issueId": "\d+"})
     * @Template()
     */
    public function viewAction($issueId)
    {
        $dbManager = $this->getDoctrine()->getManager();
        /** @var Issue $issue */
        $issue = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue')
            ->find($issueId);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $securityContext = $this->container->get('security.context');
        $isManager = $securityContext->isGranted('ROLE_ADMIN') !== false;
        $currentUser = $securityContext->getToken()->getUser();
        if (!$isManager && !$issue->getProject()->getMembers()->contains($currentUser)) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

        $comment = new Comment();
        $comment->setIssue($issue);
        $commentFormAction = $this->generateUrl('_issue_comment_create');
        $issueRepository = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue');
        $commentForm = $this->get('form.factory')
            ->create(new CommentType($commentFormAction, $issueRepository), $comment);

        return array(
            'issue' => $issue,
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * @Route("/update/{issueId}", name="_issue_update", requirements={"issueId": "\d+"})
     * @Template()
     */
    public function updateAction($issueId)
    {
        $dbManager = $this->getDoctrine()->getManager();

        /** @var Issue $issue */
        $issue = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue')
            ->find($issueId);

        if (!$issue) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $securityContext = $this->container->get('security.context');
        $isManager = $securityContext->isGranted('ROLE_ADMIN') !== false;
        $currentUser = $securityContext->getToken()->getUser();
        if (!$isManager && !$issue->getProject()->getMembers()->contains($currentUser)) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

        $projectRepository = $dbManager->getRepository('Oro\ProjectBundle\Entity\Project');
        $form = $this->get('form.factory')->create(new IssueEditType($securityContext, $projectRepository), $issue);

        $request = $this->get('request');
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            if (!$issue->getCollaborators()->contains($issue->getAssignee())) {
                $issue->addCollaborator($issue->getAssignee());
            }

            $unitOfWork = $dbManager->getUnitOfWork();
            $unitOfWork->computeChangeSets();
            $changeSet = $unitOfWork->getEntityChangeSet($issue);
            if (isset($changeSet['status'])) {
                $activity = new Activity();
                $activity->setType(Activity::TYPE_CHANGED_ISSUE)
                    ->setIssueNewStatus($issue->getStatus())
                    ->setUser($currentUser);
                $activity->setIssue($issue);
                $dbManager->persist($activity);
            }

            $dbManager->flush();

            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('The Issue Data has been saved!');

            return new RedirectResponse($this->generateUrl('_issue_view', array('issueId' => $issueId)));
        }

        return array('form' => $form->createView());
    }
}
