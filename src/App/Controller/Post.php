<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Respect\Validation\Validator as v;

class Post extends \App\Controller\Main
{

    /**
     *
     * Build page list post
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function pageList(Application $app, Request $request)
    {
        $posts = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->findAll();

        $content = null;
        if ((bool) count($posts)) {
            $elements = [];
            foreach ($posts as $post) {
                $elements[] = [$post->getId(), $post->getTitle(), $post->getPath(), $post->getUser()->getName(), $post->getCreatedAt()->format('d-m-Y h:m:i')];
            }

            $headers = ["Title", "path", "Created By", "Created at"];

            $tabela = $this->table($app, "post", $headers, $elements, false, false, true);
            $content = $this->panel($app, $tabela, "");
        }


        $alert = false;
        if ($app["session"]->get("mensagem")) {
            $alert = $app["session"]->get("mensagem");
            $app["session"]->set("mensagem", null);
        }

        $lista = $this->list($app, "Post list", 'post', $content, null, $alert);
        return new Response($lista);
    }


    /***
     *
     * Delete post
     *
     * @param Request $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost(Request $request, Application $app)
    {

        $id = $request->get('id');

        $post = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->find($id);

        if (!$post instanceof \MicroCms\Model\Entity\Post) {
            $app["session"]->set("mensagem", $this->alert($app, "Post not found", "warning"));
        } else {
            $app["session"]->set("mensagem", $this->alert($app, "Deleted post successfully", "success"));
            $app['entityManager']->remove($post);
            $app['entityManager']->flush();
        }

        return $app->redirect("/post/");
    }

    /**
     * Build form for create post
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

        $content = $app['twig']->render("post/create.twig", $data);
        $form = $this->form($app, $content);
        $panel = $this->panel($app, $form, "Post Form");
        $pagina = $this->page($app, "Create Post", $panel, $alert);
        return new Response($pagina);
    }

    /**
     *
     * Process post
     *
     * @param Request $request
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     *
     */
    public function processPost(Request $request, Application $app)
    {

        $post = $request->request->all();
        try {
            $app["session"]->set("mensagem", null);

            if (empty($post['title']) || empty($post['path']) || empty($post['body'])) {
                $app["session"]->set(
                    "mensagem",
                    $this->alert($app, "It is necessary to be informed of all three fields", "alert")
                );
                return $this->pageForm($app, $post);
            }

            $post['path'] = str_replace(' ', "", $post['path']);
            while (substr($post['path'], 0, 1) == '/') {
                $post['path'] = substr($post['path'], 1);
            }

            $post['path'] = "api/" . $post['path'];

            if ($app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->FindOneBy(['path' => $post['path']])) {
                $app["session"]->set(
                    "mensagem",
                    $this->alert($app, "The path already entered belongs to a post", "alert")
                );

                return $this->pageForm($app, $post);
            } elseif (!v::url()->validate('http://example.com/' . $post['path'])) {
                $app["session"]->set("mensagem", $this->alert($app, "The path is invalid", "warning"));

                return $this->pageForm($app, $post);
            }

            $oPost = new \MicroCms\Model\Entity\Post(
                $app["session"]->get("user"),
                $post['title'],
                $post['path'],
                $post['body']
            );

            $app['entityManager']->merge($oPost);
            $app['entityManager']->flush();
        } catch (\LogicException $e) {
            $mensagem = $e->getMessage();

            $app['monolog']->addError($mensagem);
            $app["session"]->set(
                "mensagem",
                $this->alert($app, "<b>Ops!</b>Something unexpected happened!<br/>" . $mensagem, "danger")
            );

            return $this->pageForm($app, $post);
        }

        $app['session']->set("mensagem", $this->alert($app, "Post successfully registered."));
        return $app->redirect("/post/");
    }
}
