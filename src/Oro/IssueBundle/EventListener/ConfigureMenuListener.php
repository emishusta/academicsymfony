<?php

namespace Oro\IssueBundle\EventListener;

use Oro\AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \Oro\AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('All Issues', array('route' => '_issue'));
    }
}
