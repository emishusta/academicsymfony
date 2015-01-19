<?php

namespace Oro\UserBundle\Controller;

use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/user", name="_user")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
        if (false === $securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

        //get all users but current user
        $query = $this->getDoctrine()
            ->getRepository('Oro\UserBundle\Entity\User')
            ->createQueryBuilder('u')
            ->where('u.id != :userId')
            ->setParameter('userId', $securityContext->getToken()->getUser()->getId())
            ->getQuery();

        $users = $query->getResult();

        return array('users' => $users);
    }

    /**
     * @Route("/user/create", name="_user_create")
     * @Template()
     */
    public function createAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

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

                $flash = $this->get('braincrafted_bootstrap.flash');
                $flash->success('The User has been saved!');

                return new RedirectResponse($this->generateUrl('_user'));
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/user/update/{userId}", name="_user_update", requirements={"userId": "\d+"})
     * @Template()
     */
    public function updateAction($userId)
    {
        $securityContext = $this->container->get('security.context');
        $isCurrentUser = $securityContext->getToken()->getUser()->getId() == $userId;
        if (false === $securityContext->isGranted('ROLE_SUPER_ADMIN') && !$isCurrentUser) {
            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->alert('Access Denied!');
            return array();
        }

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
        $options['first_options'] = array_merge(
            $options['first_options'],
            array('attr' => array('help_text' => 'Leave empty to use current.'))
        );
        $options['second_options'] = array_merge(
            $options['second_options'],
            array('attr' => array('help_text' => 'Leave empty to use current.'))
        );
        $form->add('password', $type, $options)
            ->add('save', 'submit', array('label' => 'Update'))
            ->add('avatar_file', 'file', array(
                    'required' => false,
                    'attr' => array('help_text' => 'Leave empty to use current.')
                )
            );

        $request = $this->get('request');
        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            if ($user->getPassword()) {
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
            } else {
                $user->setPassword($originalPassword);
            }

            $dbManager->flush();

            $flash = $this->get('braincrafted_bootstrap.flash');
            $flash->success('The User Data has been saved!');

            if ($isCurrentUser) {
                $url = $this->generateUrl('_user_update', array('userId' => $userId));
            } else {
                $url = $this->generateUrl('_user');
            }

            return new RedirectResponse($url);
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/login", name="_user_login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $request->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $data = array(
            '_username' => $request->getSession()->get(SecurityContext::LAST_USERNAME)
        );

        $form =  $this->get('form.factory')
            ->createNamedBuilder(null, 'form', $data, array('action' => $this->generateUrl('login_check')))
            ->add('_username', 'text')
            ->add('_password', 'password')
            ->add('login', 'submit', array('label' => 'Login'))
            ->getForm();

        if (!empty($error)) {
            $formError = new FormError($error->getMessage());
            $form->addError($formError);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/user/view/{userId}", name="_user_view", requirements={"userId": "\d+"})
     * @Template()
     */
    public function viewAction($userId)
    {
        $dbManager = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $dbManager->getRepository('Oro\UserBundle\Entity\User')
            ->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $issues = $dbManager->getRepository('OroIssueBundle:Issue')
            ->getIssuesAssignedToUser($userId)->getQuery()->getResult();

        return array(
            'user' => $user,
            'issues' => $issues
        );
    }
}
