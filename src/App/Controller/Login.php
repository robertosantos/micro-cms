<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \MicroCms\Model\Entity\User;

class Login extends \App\Controller\Main
{
    /**
     * Registers a session of a user after they authenticate
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Silex\Application $app
     */
    public function register(Request $request, Application $app)
    {
        $username = $request->get('AUTH_USER');
        $password = md5($request->get('AUTH_PW'));

        try {
            $user = new User($username, $password);
            $user = $this->isValid($app, $user);

            if ($user instanceof User) {
                $app["session"]->set("user", $user);
                $app["session"]->remove("mensagem");
                return $app->redirect("/home/");
            } else {
                $app["session"]->set("mensagem", $this->alert($app, "Invalid credential.", "warning"));
                $app["monolog"]->addInfo("Credential invalid: username:$username - passwd $password");
                return $app->redirect("/login/");
            }
        } catch (\Exception $e) {
            $app["monolog"]->addWarning("Error in login: " . $e->getMessage());
            $app["session"]->set("mensagem", $this->alert($app, "Sorry, the system is down, try again later.", "danger"));
            return $app->redirect("/login/");
        }
    }

    /**
     * Displays the login screen
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Silex\Application $app
     * @return string HTML
     */
    public function signup(Request $request, Application $app)
    {

        if (self::isLogged($app)) {
            return $app->redirect("/home/");
        }

        $message = $app["session"]->get("mensagem");
        $app["session"]->remove("mensagem");

        $pagina = $app["twig"]->render("login/form.twig", array("mensagem" => $message));
        return new Response($pagina);
    }


    /**
     * Registers the user output of the system
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Silex\Application $app
     */
    public function signout(Request $request, Application $app)
    {
        if (is_null($app['session']->get('user'))) {
            return $app->redirect('/login/');
        }

        $app["session"]->remove("user");
        $app["session"]->remove("mensagem");
        return $app->redirect("/login/");
    }

    /**
     * Check if user is logged in
     * @param \Silex\Application $app
     * @return boolean
     */
    public static function isLogged(Application $app)
    {
        if (!($app['session']->get('user') instanceof User)) {
            return false;
        }
        return true;
    }

    /**
     *  Validates the user credential
     *
     * @param \MicroCms\Model\Repository\User $user
     * @return boolean|User
     */
    private function isValid(Application $app, User &$user)
    {
        return $app['entityManager']->getRepository("\MicroCms\Model\Entity\User")->isValid($user);
    }
}
