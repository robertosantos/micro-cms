<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Home extends \App\Controller\Main
{

    /**
     * Build form home
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function pageForm(Application $app, Request $request)
    {
        $alert = $app['session']->get("mensagem");
        $app['session']->set("mensagem", null);

        $content = $app['twig']->render("home/form.twig");
        $panel = $this->panel($app, $content, "Welcome \o/");
        $page = $this->page($app, "Sample MicroCMS", $panel, $alert);

        return new Response($page);
    }
}
