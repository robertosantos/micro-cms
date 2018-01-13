<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class User extends \App\Controller\Main
{
    /**
     *
     * Build list user
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function pageList(Application $app, Request $request)
    {
        $users = $app['entityManager']->getRepository("MicroCms\Model\Entity\User")->findAll();

        $content = null;
        if ((bool) count($users)) {
            $elements = [];
            foreach ($users as $user) {
                $elements[] = [$user->getId(), $user->getName(), $user->getCreatedAt()->format('d-m-Y h:m:i')];
            }

            $headers = ["Name", "Created at"];

            $tabela = $this->table($app, "user", $headers, $elements, false, false, true);
            $content = $this->panel($app, $tabela, "");
        }


        $alert = false;
        if ($app["session"]->get("mensagem")) {
            $alert = $app["session"]->get("mensagem");
            $app["session"]->set("mensagem", null);
        }

        $lista = $this->list($app, "User list", 'user', $content, null, $alert);
        return new Response($lista);
    }

    /**
     *
     * Process delete
     *
     * @param Request $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost(Request $request, Application $app)
    {

        $id = $request->get('id');

        if ($id == 1) {
            $app["session"]->set("mensagem", $this->alert($app, "It is not allowed to delete admin", "alert"));
            return $app->redirect("/user/");
        }


        $user = $app['entityManager']->getRepository("MicroCms\Model\Entity\User")->find($id);

        if (!$user instanceof \MicroCms\Model\Entity\User) {
            $app["session"]->set("mensagem", $this->alert($app, "User not found", "warning"));
        } else {
            $app["session"]->set("mensagem", $this->alert($app, "Deleted user successfully", "success"));
            $app['entityManager']->remove($user);
            $app['entityManager']->flush();
        }

        return $app->redirect("/user/");
    }

    /**
     *
     * Build form create
     *
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function pageCreate(Request $request, Application $app)
    {
        return $this->pageForm($app);
    }

    /**
     *
     * Mount form
     *
     * @param Application $app
     * @param array $data
     * @return Response
     */
    public function pageForm(Application $app, $data = [])
    {

        $alert = $app["session"]->get("mensagem");
        $app["session"]->set("mensagem", null);

        $content = $app['twig']->render("user/create.twig", $data);
        $form = $this->form($app, $content);
        $panel = $this->panel($app, $form, "Registration Form");
        $pagina = $this->page($app, "Create User", $panel, $alert);
        return new Response($pagina);
    }

    /**
     *
     * Process post
     *
     * @param Request $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function processPost(Request $request, Application $app)
    {

        $post = $request->request->all();
        try {
            if (empty($post['name']) || empty($post['password']) || empty($post['passwordConfirm'])) {
                $app["session"]->set("mensagem", $this->alert($app, "It is necessary to be informed of all three fields", "alert"));
                return $this->pageForm($app, $post);
            } elseif ($post['password'] != $post['passwordConfirm']) {
                $app["session"]->set("mensagem", $this->alert($app, "The passwords are different &nbsp; =/", "alert"));
                return $this->pageForm($app, $post);
            }
            $app["session"]->set("mensagem", null);

            if ($app['entityManager']->getRepository("MicroCms\Model\Entity\User")->FindOneBy(['name' => $post['name']])) {
                $app["session"]->set("mensagem", $this->alert($app, "The name already entered belongs to a user", "alert"));
                return $this->pageForm($app, $post);
            }

            $user = new \MicroCms\Model\Entity\User($post['name'], md5($post['password']));

            $app['entityManager']->persist($user);
            $app['entityManager']->flush();
        } catch (\LogicException $e) {
            $mensagem = $e->getMessage();

            $app['monolog']->addError($mensagem);
            $app["session"]->set("mensagem", $this->alert($app, "<b>Ops!</b>Something unexpected happened!<br/>" . $mensagem, "danger"));

            return $this->pageForm($app, $post);
        }

        $app['session']->set("mensagem", $this->alert($app, "User successfully registered."));
        return $app->redirect("/user/");
    }
}
