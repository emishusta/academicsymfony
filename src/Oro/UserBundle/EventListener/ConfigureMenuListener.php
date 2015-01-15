<?php

namespace Oro\UserBundle\EventListener;

use AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();
        $securityContext = $event->getSecurityContext();

        $user = $securityContext->getToken()->getUser();
        $key = 'Welcome, ' . $user->getFullname();

        if ($securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $menu->addChild('Users', array('route' => '_user'));
        }

        $menu->addChild($key)->setAttribute('dropdown', true);
        $menu->getChild($key)->addChild(
            'View Profile',
            array(
                'route' => '_user_view',
                'routeParameters' => array('userId' => $user->getId())
            )
        );
        $menu->getChild($key)->addChild(
            'Edit Profile',
            array(
                'route' => '_user_update',
                'routeParameters' => array('userId' => $user->getId())
            )
        );
        $menu->getChild($key)->addChild('Logout', array('route' => 'logout'));
    }
}
