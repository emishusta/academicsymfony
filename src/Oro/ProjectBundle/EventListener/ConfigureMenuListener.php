<?php

namespace Oro\ProjectBundle\EventListener;

use AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Projects List', array('route' => '_project'));
    }
}
