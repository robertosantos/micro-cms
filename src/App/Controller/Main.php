<?php

namespace App\Controller;

abstract class Main
{

    /**
     * Auxiliary method for table mounting
     * @param \Silex\Application $app
     * @param string $link
     * @param array $headers headers of table
     * @param array $contents content line
     * @param bool $edit
     * @param bool $view
     * @param bool $delete
     * @param string $style
     * @return string HTML
     */
    public function table(
        \Silex\Application $app,
        $link,
        $headers = array(),
        $contents = array(),
        $edit = true,
        $view = true,
        $delete = false,
        $style = "table-hover"
    ) {
        return $app["twig"]->render("helper/table.twig", [
            "style"=>$style,
            "link"=>$link,
            "headers" => $headers,
            "contents" => $contents,
            "editar"=>$edit,
            "visualizar"=>$view,
            "deletar"=>$delete
        ]);
    }

    /**
     * Build a standard page
     * @param \Silex\Application $app
     * @param string $title Title of
     * @param string $content Page content
     * @param string $alert (mensagem) alert to be displayed
     * @return string HTML content
     */
    public function page(\Silex\Application $app, $title, $content, $alert = null)
    {
        return $app["twig"]->render("helper/page.twig", [
            "alert"=>$alert,
            "title"=>$title,
            "content"=>$content,
            "nav" => $this->navbar($app),
        ]);
    }

    /**
     * Build panel
     * @param \Silex\Application $app
     * @param string $content
     * @param string $title
     * @param string $footer
     * @return string HTML com panel
     */
    public function panel(\Silex\Application $app, $content, $title = null, $footer = null)
    {
        return $app["twig"]->render("helper/panel.twig", [
            "title"=>$title,
            "content"=>$content,
            "footer"=>$footer
        ]);
    }

    /**
     * Build form
     * @param \Silex\Application $app
     * @param string $content
     * @return string HTML
     */
    public function form(\Silex\Application $app, $content)
    {
        return $app["twig"]->render("helper/form.twig", [
            "content"=>$content
        ]);
    }

    /**
     * Build list
     * @param \Silex\Application $app
     * @param string $title
     * @param string $link
     * @param string $content
     * @param string $mensagem
     * @param string $alert
     * @return string HTML
     */
    public function list(\Silex\Application $app, $title, $link, $content, $mensagem = null, $alert = null)
    {
        return $app["twig"]->render("helper/list.twig", [
            "alert"=>$alert,
            "title"=>$title,
            "link"=>$link,
            "content"=>$content,
            "mensagem"=>$mensagem,
            "nav" => $this->navbar($app)
        ]);
    }

    /**
     * Build alert
     * @param \Silex\Application $app
     * @param string $mensagem
     * @param string $type ( success,warning,danger,info )
     * @return string HTML
     */
    public function alert(\Silex\Application $app, $mensagem, $type = "success")
    {
        if (!in_array($type, ["success","warning","danger","info"])) {
            $type = "info";
        }

        return $app["twig"]->render("helper/mensagem.twig", [
            "type"=>$type,
            "mensagem"=>$mensagem
        ]);
    }


    /**
     * Build navbar
     * @param \Silex\Application $app
     * @return string
     */
    public function navbar(\Silex\Application $app)
    {
        $user = $app["session"]->get("user");
        $user = $user->getName();
        return $app["twig"]->render('user/nav.twig', ["user" => $user]);
    }
}
