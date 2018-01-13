<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class API extends \App\Controller\Main
{

    public function listPost(Request $request, Application $app)
    {

        $statusCode = 200;
        $contentType = getenv('API_CONTENT_TYPE_OUTPUT');
        $limit = 10;
        $offSet = 0;
        $queryString = $request->query->all();

        if (isset($queryString['limit']) && is_numeric($queryString['limit'])) {
            $limit = $queryString['limit'];
        }

        if (isset($queryString['offSet']) && is_numeric($queryString['offSet'])) {
            $offSet = $queryString['offSet'];
        }

        $posts = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->getPost($limit, $offSet);

        $elements = [];
        foreach ($posts as $post) {
            $createdAt = $post->getCreatedAt() instanceof \DateTime ? $post->getCreatedAt()->format(\DateTime::ISO8601) : '';
            $updatedAt = $post->getUpdatedAt() instanceof \DateTime ? $post->getUpdatedAt()->format(\DateTime::ISO8601) : '';

            $elements[] = ["id" => $post->getId(), "title" => $post->getTitle(), "path" => $post->getPath(),
            "body" => $post->getBody(), "createdAt" => $createdAt, "updatedAt" => $updatedAt];
        }

        if (empty($elements)) {
            $statusCode = 404;
        }

        $elements = json_encode($elements);
        return new Response($elements, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
    }

    public function getPost(Request $request, Application $app)
    {

        $statusCode = 200;
        $contentType = getenv('API_CONTENT_TYPE_OUTPUT');

        if (!is_numeric($request->get('id'))) {
            $statusCode = 406;
            $element = json_encode(["Error" => "ID need be numeric"]);
        } else {
            $post = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->find($request->get('id'));

            if ($post instanceof \MicroCms\Model\Entity\Post) {
                $element = $app['serializer']->serialize($post, $contentType);
            } else {
                $statusCode = 404;
                $element = "";
            }
        }

        return new Response($element, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
    }

    public function deletePost(Request $request, Application $app)
    {

        $statusCode = 200;
        $element = "";
        $contentType = getenv('API_CONTENT_TYPE_OUTPUT');

        if (!is_numeric($request->get('id'))) {
            $statusCode = 406;
            $element = json_encode(["Error" => "ID need be numeric"]);
        } else {
            $post = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->find($request->get('id'));

            if ($post instanceof \MicroCms\Model\Entity\Post) {
                $app['entityManager']->remove($post);
                $app['entityManager']->flush();
            } else {
                $statusCode = 404;
            }
        }

        return new Response($element, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
    }

    public function putPost(Request $request, Application $app)
    {

        $statusCode = 200;
        $contentType = getenv('API_CONTENT_TYPE_OUTPUT');
        $data = $request->request->all();


        if (isset($data['title']) && isset($data['path']) && isset($data['body'])
            && !empty($data['title']) && !empty($data['path']) && !empty($data['body'])) {
            if (!is_numeric($request->get('id'))) {
                $statusCode = 406;
                $element = json_encode(["Error" => "ID need be numeric"]);
            } else {
                $post = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->find($request->get('id'));

                if ($post instanceof \MicroCms\Model\Entity\Post) {
                    $post->setTitle($data['title']);
                    $post->setPath($data['path']);
                    $post->setBody($data['body']);
                    $post->setUpdatedAt(new \DateTime('now'));

                    $app['entityManager']->persist($post);
                    $app['entityManager']->flush();

                    $element = $app['serializer']->serialize($post, $contentType);
                } else {
                    $statusCode = 404;
                    $element = "";
                }
            }
        } else {
            $statusCode = 406;
            $element = json_encode(["Error" => "Need inform title, path and body"]);
        }

        return new Response($element, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
    }

    public function createPost(Request $request, Application $app)
    {

        $statusCode = 201;
        $contentType = getenv('API_CONTENT_TYPE_OUTPUT');
        $data = $request->request->all();


        if (isset($data['title']) && isset($data['path']) && isset($data['body']) && isset($data['user'])
            && !empty($data['title']) && !empty($data['path']) && !empty($data['body']) && !empty($data['user'])) {
            $user = $app['entityManager']->getRepository("\MicroCms\Model\Entity\User")->find($data['user']);
            if ($user instanceof \MicroCms\Model\Entity\User) {
                $data['path'] = str_replace(' ', "", $data['path']);
                while (substr($data['path'], 0, 1) == '/') {
                    $data['path'] = substr($data['path'], 1);
                }
                $data['path'] = "api/" . $data['path'];

                if ($app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->FindOneBy(['path' => $data['path']])) {
                    $element = json_encode(["Error" => "The path already entered belongs to a post"]);
                    $statusCode = 406;
                } else {
                    $post = new \MicroCms\Model\Entity\Post(
                        $user,
                        $data['title'],
                        $data['path'],
                        $data['body']
                    );

                    $app['entityManager']->persist($post);
                    $app['entityManager']->flush();

                    $element = $app['serializer']->serialize($post, $contentType);
                }
            } else {
                $statusCode = 404;
                $element = json_encode(["Error" => "User Not Found"]);
            }
        } else {
            $statusCode = 406;
            $element = json_encode(["Error" => "Need inform user, title, path and body"]);
        }

        return new Response($element, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
    }
}
