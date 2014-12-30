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
        return array();
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
                $dbManager = $this->getDoctrine()->getManager();
                $user->setAvatar('test');
                $dbManager->persist($user);
                $dbManager->flush();

                $session = $this->getRequest()->getSession();
                $session->getFlashBag()->add('message', 'New User has been added!');


                return new RedirectResponse($this->generateUrl('_user'));
            }
        }

        return array('form' => $form->createView());
    }
}
