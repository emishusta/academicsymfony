<?php

namespace Oro\IssueBundle\Controller;

use Oro\IssueBundle\Entity\Comment;
use Oro\IssueBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommentController extends Controller
{
    /**
     * @Route("/create", name="_issue_comment_create")
     * @Template("OroIssueBundle:Comment:list.html.twig")
     */
    public function createAction()
    {
        $comment = new Comment();
        $formAction = $this->generateUrl('_issue_comment_create');
        $dbManager = $this->getDoctrine()->getManager();
        $issueRepository = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue');
        $form = $this->get('form.factory')->create(new CommentType($formAction, $issueRepository), $comment);

        $issue = null;
        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            $issue = $form->getData()->getIssue();
            if ($form->isValid()) {
                $securityContext = $this->container->get('security.context');
                $currentUser = $securityContext->getToken()->getUser();
                $comment->setAuthor($currentUser);
                if (!$comment->getIssue()->getCollaborators()->contains($currentUser)) {
                    $comment->getIssue()->addCollaborator($currentUser);
                }

                $dbManager->persist($comment);
                $dbManager->flush();
            }
        }

        return array(
            'issue' => $issue
        );
    }

    /**
     * @Route("/update/{commentId}", name="_issue_comment_update", requirements={"commentId": "\d+"})
     * @Template("OroIssueBundle:Comment:list.html.twig")
     */
    public function updateAction($commentId)
    {
        $dbManager = $this->getDoctrine()->getManager();

        $comment = $dbManager->getRepository('Oro\IssueBundle\Entity\Comment')
            ->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $securityContext = $this->container->get('security.context');
        $isAdmin = $securityContext->isGranted('ROLE_SUPER_ADMIN') !== false;
        $currentUser = $securityContext->getToken()->getUser();
        if (!$isAdmin && $currentUser->getId() != $comment->getAuthor()->getId() ) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');

            return $this->redirect($this->generateUrl('_issue_view', array('issueId' => $comment->getIssue()->getId())));
        }

        $issueRepository = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue');
        $form = $this->get('form.factory')->create(new CommentType('', $issueRepository), $comment);

        $issue = null;
        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            $issue = $form->getData()->getIssue();
            if ($form->isValid()) {
                $dbManager->flush();
            }
        }

        return array(
            'issue' => $issue
        );
    }

    /**
     * @Route("/delete/{commentId}", name="_issue_comment_delete", requirements={"commentId": "\d+"})
     * @Template("OroIssueBundle:Comment:list.html.twig")
     */
    public function deleteAction($commentId)
    {

        $dbManager = $this->getDoctrine()->getManager();

        $comment = $dbManager->getRepository('Oro\IssueBundle\Entity\Comment')
            ->find($commentId);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Issue entity.');
        }

        $securityContext = $this->container->get('security.context');
        $isAdmin = $securityContext->isGranted('ROLE_SUPER_ADMIN') !== false;
        $currentUser = $securityContext->getToken()->getUser();
        if (!$isAdmin && $currentUser->getId() != $comment->getAuthor()->getId() ) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');

            return $this->redirect(
                $this->generateUrl('_issue_view', array('issueId' => $comment->getIssue()->getId()))
            );
        }

        $issue = $dbManager->getRepository('Oro\IssueBundle\Entity\Issue')
            ->find($comment->getIssue()->getId());

        $dbManager->remove($comment);
        $dbManager->flush();

        return array(
            'issue' => $issue
        );
    }
}
