<?php

session_start();
define('APP_DIRECTORY', __DIR__ . '/');

require APP_DIRECTORY . 'vendor/autoload.php';



// todo : A charger dans un autoloader plus tard
require_once APP_DIRECTORY . 'models/connectDB.php';
require_once APP_DIRECTORY . 'models/Articles.php';
require_once APP_DIRECTORY . 'models/Comments.php';
require_once APP_DIRECTORY . 'models/User.php';
require_once APP_DIRECTORY . 'controllers/BaseController.php';
require_once APP_DIRECTORY . 'controllers/IndexController.php';
require_once APP_DIRECTORY . 'controllers/PostsController.php';
require_once APP_DIRECTORY . 'controllers/UsersController.php';




// on défini nos routes ici
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    // page d'accueil
    $r->addRoute('GET', '/', IndexController::class . '/index');

    // Page des posts
    $r->addRoute('GET', '/posts/', PostsController::class . '/index');

    // Page détail d'un post
    $r->addRoute('GET', '/posts/{id:\d+}', PostsController::class . '/detail');

    // fonction d'ajout d'un commentaire
    $r->addRoute('POST', '/posts/{id:\d+}/addComment', PostsController::class . '/addComment');

    // Login Page
    $r->addRoute('GET', '/users/login', UsersController::class . '/indexAuth');

    // Authentication function
    $r->addRoute('POST', '/users/admin', UsersController::class . '/userAuth');

    // Inscription Page
    $r->addRoute('GET', '/users/inscription', UsersController::class . '/indexIns');

    // Inscription function
    $r->addRoute('POST', '/users/inscription/check', UsersController::class . '/insCheck');

    // Add Article function
    $r->addRoute('POST', '/users/admin/gestion', PostsController::class . '/addArticle');

    // Delete Article function
    $r->addRoute('GET', '/users/admin/delete/{id:\d+}', PostsController::class . '/delArticle');

    // Update Article function
    $r->addRoute('GET', '/users/admin/update/{id:\d+}', PostsController::class . '/updateArticle');



});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);



$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        // Todo : definir une page d'erreur
        echo 'PAGE NOT FOUND';
        break;
    

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        die('405');
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        list($class, $method) = explode("/", $handler, 2);

        // on appelle automatique notre controlleur, avec la bonne méthode et les bons paramètres donnés à notre fonction
        // Exemple pour la syntaxe "IndexController::class . '/index'", voici ce qui sera appelé : "IndexController->index()"

        call_user_func_array(array(new $class, $method), $vars);
        break;
}