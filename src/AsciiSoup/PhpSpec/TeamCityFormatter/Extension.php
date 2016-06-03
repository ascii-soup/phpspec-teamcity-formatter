<?php

namespace AsciiSoup\PhpSpec\TeamCityFormatter;

use PhpSpec\Extension\ExtensionInterface;
use PhpSpec\ServiceContainer;

/**
 * Extension
 *
 * @package TeamCityFormatter
 *
 * @author Nils Luxton <ascii.soup@gmail.com>
 */
class Extension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ServiceContainer $container)
    {
        $this->addFormatter($container, 'teamcity', 'AsciiSoup\PhpSpec\TeamCityFormatter\Formatter\TeamCityFormatter');
    }

    /**
     * Add a formatter to the service container
     *
     * @param ServiceContainer $container
     * @param string           $name
     * @param string           $class
     */
    protected function addFormatter(ServiceContainer $container, $name, $class)
    {
        $container->set('formatter.formatters.teamcity', function ($c) use ($class) {
            /** @var ServiceContainer $c */
            return new $class(
                $c->get('formatter.presenter'),
                $c->get('console.io'),
                $c->get('event_dispatcher.listeners.stats')
            );
        });
    }
}


