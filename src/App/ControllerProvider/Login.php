<?php

namespace App\ControllerProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class Login implements ControllerProviderInterface
{

    /**
     *
     * Mapping routes for login
     *
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $className = "App\Controller\Login";

        $controllers->get('', $className . '::signup')->method('GET');
        $controllers->get('/register', $className . '::register')->method('POST');
        $controllers->get('/signout', $className . '::signout')->method('GET');

        return $controllers;
    }
}
