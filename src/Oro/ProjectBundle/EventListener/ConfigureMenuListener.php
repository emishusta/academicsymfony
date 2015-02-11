<?php

namespace Oro\ProjectBundle\EventListener;

use Oro\AppBundle\Event\ConfigureMenuEvent;

class ConfigureMenuListener
{
    /**
     * @param \Oro\AppBundle\Event\ConfigureMenuEvent $event
     */
    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('Projects List', array('route' => '_project'));
    }
}
