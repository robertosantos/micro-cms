<?php

namespace App\ControllerProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Home implements ControllerProviderInterface
{

    /**
     *
     * Mapping Routes for Home
     *
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $className = "App\Controller\Home";

        $controllers->get('/', $className . '::pageForm')->method('GET');

        return $controllers;
    }
}
