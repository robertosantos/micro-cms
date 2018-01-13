<?php

namespace App\ControllerProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class API implements ControllerProviderInterface
{

    /**
     *
     * Mapping routes API
     *
     * @param Application $app
     * @return mixed
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $className = "App\Controller\API";

        $controllers->get('/posts', $className . '::listPost')->method('GET');
        $controllers->get('/posts', $className . '::createPost')->method('POST');
        $controllers->get('/posts/{id}', $className . '::getPost')->method('GET');
        $controllers->get('/posts/{id}', $className . '::putPost')->method('PUT');
        $controllers->get('/posts/{id}', $className . '::deletePost')->method('DELETE');

        return $controllers;
    }
}
