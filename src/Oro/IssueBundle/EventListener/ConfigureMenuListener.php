<?php

namespace Oro\IssueBundle\EventListener;

use AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('All Issues', array('route' => '_issue'));
    }
}
