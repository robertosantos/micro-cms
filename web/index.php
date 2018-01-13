<?php

defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__DIR__)). DIRECTORY_SEPARATOR);
defined('VIEW_PATH') || define('VIEW_PATH', realpath(dirname(__DIR__)). DIRECTORY_SEPARATOR . 'src/App/View');

require_once ROOT_PATH . 'vendor/autoload.php';

use \WhoopsSilex\WhoopsServiceProvider;
use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\JsonResponse;
use \Symfony\Component\Debug\ErrorHandler;
use \Symfony\Component\Debug\ExceptionHandler;
use \Silex\Provider\SerializerServiceProvider;
use \Silex\Provider\MonologServiceProvider;
use \Moust\Silex\Provider\CacheServiceProvider;
use \Silex\Provider\TwigServiceProvider;
use \Silex\Provider\SessionServiceProvider;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

use \MicroCms\System\DoctrineFactory;
use \App\ControllerProvider\Login;
use \App\ControllerProvider\Post;
use \App\ControllerProvider\User;
use \App\ControllerProvider\API;
use \App\ControllerProvider\Home;

# GET environment var
$dotenv = new Dotenv\Dotenv(ROOT_PATH);
$dotenv->load();

# Create Silex instance
$app = new Silex\Application();
$app['debug'] = getenv('DEBUG');

# Setup Doctrine Provider
require_once ROOT_PATH . 'resource' . DIRECTORY_SEPARATOR . 'database.php';
$app->register(new DoctrineFactory(), array("entityManager" => $entityManager));

# payload to json
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

# Setup Monolog
$now = new \Datetime('now');
$now = $now->format('d-m-Y');
$monologLevel = getenv('MONOLOG_LEVEL');
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => ROOT_PATH . "storage/$now.log",
    'monolog.level' => constant("\Monolog\Logger::{$monologLevel}")
));

# Setup Memcached
$app->register(new CacheServiceProvider(), array(
    'caches.options' => array(
        'memcached' => array(
            'driver' => 'memcached'
        )
    )
));

# Setup Serializer
$app->register(new SerializerServiceProvider());
$app['serializer.normalizers'] = function ($app) {

    # Set format date with ISO 8601
    $dateTimeCallback = function ($dateTime) {
        return $dateTime instanceof \DateTime ? $dateTime->format(\DateTime::ISO8601) : '';
    };

    $userCallback = function ($user) {

        if ($user instanceof \MicroCms\Model\Entity\User) {
            $user = $user->getName();
        }

        return $user;
    };

    $normalizer = new \Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer();
    $normalizer->setCallbacks([
        "createdAt" => $dateTimeCallback,
        "updatedAt" => $dateTimeCallback,
        "user" => $userCallback
    ]);

    return [$normalizer];
};

# Setup Twig
$app->register(new TwigServiceProvider(), array(
    'twig.path' => VIEW_PATH,
));

# Setup Session
$app->register(new SessionServiceProvider());

# check acess
$app->before(function (Request $request, \Silex\Application $app) {
    $uri = $request->getRequestUri();

    if (!empty($uri) && !(strpos($uri, '/login/') !== false) && !(strpos($uri, '/api/')!== false)) {
        if (!App\Controller\Login::isLogged($app)) {
            $app["session"]->set("mensagem", "Fist, do login");
            return $app->redirect("/login/");
        }
    }

    if (strpos($uri, '/api/') !== false) {
        $authUser = $request->headers->get("PHP_AUTH_USER");
        $authPass = $request->headers->get("PHP_AUTH_PW");

        if ($authUser != "dXNlci1taWNyby1jbXMtMjAxNw==" || $authPass != "cGFzc3dvcmQtbWljcm8tY21zLTIwMTc==") {
            return new JsonResponse(["message" => "Invalid credentials."], 403);
        }
    }
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    $contentType = "json";
    switch ($code) {
        case 404:
            $statusCode = 404;

            $uri = substr($request->getRequestUri(), 1);

            $post = $app['entityManager']->getRepository("MicroCms\Model\Entity\Post")->FindOneBy(['path' => $uri]);
            if ($post instanceof MicroCms\Model\Entity\Post) {
                $authUser = $request->headers->get("PHP_AUTH_USER");
                $authPass = $request->headers->get("PHP_AUTH_PW");

                if ($authUser != "dXNlci1taWNyby1jbXMtMjAxNw==" || $authPass != "cGFzc3dvcmQtbWljcm8tY21zLTIwMTc==") {
                    return new JsonResponse(["message" => "Invalid credentials."], 403);
                }

                $statusCode = 200;
                $element = $app['serializer']->serialize($post, "json");
            } else {
                $element = json_encode(["Error" => 'The requested page could not be found.']);
            }
            break;
        default:
            $element = json_encode(["Error" => 'We are sorry, but something went terribly wrong.']);
            $statusCode = 400;
    }

    return new Response($element, $statusCode, array("Content-Type" => $request->getMimeType($contentType)));
});

# Setup ControllerProviders
$app->mount('/login', new Login());
$app->mount('/api', new API());
$app->mount('/user', new User());
$app->mount('/post', new Post());
$app->mount('/home', new Home());

$app->run();
