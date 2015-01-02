<?php

namespace Oro\UserBundle\Controller;

use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends Controller
{
    /**
     * @Route("/user", name="_user")
     * @Template()
     */
    public function indexAction()
    {
        $users = $this->getDoctrine()
            ->getRepository('Oro\UserBundle\Entity\User')
            ->findAll();

        return array('users' => $users);
    }

    /**
     * @Route("/user/new", name="_user_new")
     * @Template()
     */
    public function newAction()
    {
        $user = new User();
        $form = $this->get('form.factory')->create(new UserType(), $user);

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
                $dbManager = $this->getDoctrine()->getManager();
                $dbManager->persist($user);
                $dbManager->flush();

                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('message', 'The User has been saved!');

                return new RedirectResponse($this->generateUrl('_user'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/edit/{userId}", name="_user_edit", requirements={"userId": "\d+"})
     * @Template()
     */
    public function editAction($userId)
    {
        $dbManager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $dbManager->getRepository('Oro\UserBundle\Entity\User')
            ->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }

        $originalPassword = $user->getPassword();

        $form = $this->get('form.factory')->create(new UserType(), $user);

        $passwordField = $form->get('password');
        $options = $passwordField->getConfig()->getOptions();
        $type = $passwordField->getConfig()->getType()->getName();
        $options['required'] = false;
        $form->add('password', $type, $options)
            ->add('save', 'submit', array('label' => 'Update User'))
            ->add('avatar_file', 'file', array('required' => false));

        $request = $this->get('request');
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            if ($user->getPassword()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
            } else {
                $user->setPassword($originalPassword);
            }

            $dbManager->flush();

            $session = $this->getRequest()->getSession();
            $session->getFlashBag()->add('message', 'The User Data has been saved!');

            return new RedirectResponse($this->generateUrl('_user'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/delete/{userId}", name="_user_delete", requirements={"userId": "\d+"})
     */
    public function deleteAction($userId)
    {
        $dbManager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $dbManager->getRepository('Oro\UserBundle\Entity\User')
            ->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }

        $dbManager->remove($user);
        $dbManager->flush();

        $session = $this->getRequest()->getSession();
        $session->getFlashBag()->add('message', 'The User has been deleted!');
        return new RedirectResponse($this->generateUrl('_user'));
    }
}
