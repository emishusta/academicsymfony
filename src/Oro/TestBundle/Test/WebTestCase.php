<?php

namespace Oro\TestBundle\Test;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseTestCase;
use Symfony\Component\Console\Input\StringInput;

class WebTestCase extends BaseTestCase
{
    protected static $environment='test';

    protected static $application;

    protected static $isInitedDatabaseFixtures = false;

    protected function setUp()
    {
        self::initDatabaseFixtures();
    }

    protected static function initDatabaseFixtures()
    {
        if (!self::$isInitedDatabaseFixtures) {
            self::runCommand('doctrine:database:drop');
            self::runCommand('doctrine:database:create');
            self::runCommand('doctrine:schema:update --force');
            self::runCommand('doctrine:fixtures:load --no-interaction');
            self::$isInitedDatabaseFixtures = true;
        }
    }

    protected static function runCommand($command)
    {
        $env = '--env=' . self::$environment;
        $command = sprintf('%s --quiet %s', $command, $env);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    public static function getAdminAuthHeader()
    {
        return array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => '123123q'
        );
    }

    /**
     * Extract parameter from url
     *
     * @param string $url
     * @param string $param
     * @return string|null
     */
    protected function getParamFromUrl($url, $param)
    {
        $path = parse_url($url, PHP_URL_PATH);
        $client = static::createClient();
        $router = $client->getContainer()->get('router')->match($path);
        return (isset($router[$param])) ? $router[$param] : null;
    }
}

