<?php

namespace App\ControllerProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class User implements ControllerProviderInterface
{

    /**
     *
     * Mapping routers for user
     *
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $className = "App\Controller\User";

        $controllers->get('/', $className . '::pageList')->method('GET');
        $controllers->get('/delete/{id}', $className . '::deletePost')->method('GET');
        $controllers->get('/create', $className . '::pageCreate')->method('GET');
        $controllers->get('/create', $className . '::processPost')->method('POST');

        return $controllers;
    }
}
