<?php

namespace AppBundle\Menu;

use AppBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class MainBuilder extends ContainerAware
{
    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function build(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Dashboard', array('route' => 'homepage'));

        $securityContext = $this->container->get('security.context');
        $this->container
            ->get('event_dispatcher')
            ->dispatch(
                ConfigureMenuEvent::CONFIGURE,
                new ConfigureMenuEvent($factory, $menu, $securityContext)
            );

        return $menu;
    }
}
