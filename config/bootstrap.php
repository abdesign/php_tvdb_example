<?php


use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use Doctrine\ORM\EntityManager;
use FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;



//Autoload for Composer Packages
$composerAutoload = BASEPATH . "/vendor/autoload.php";

if (file_exists($composerAutoload)) {
    $loader = require $composerAutoload;
} else {
    throw new \Exception(
        'Run \'composer install\' before executing.'
    );
}

$config = new \App\Helper\Config();

//Initialize doctrine
$configuration = DoctrineSetup::createXMLMetadataConfiguration([BASEPATH."/config/xml"], $isDevMode = true, null, null, false);
$em = EntityManager::create($config->getDbConfig(), $configuration);

// The FastRoute Library was used to expadite development
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('GET', '/', 'App\Http\Index\IndexPresenter');
    $r->addRoute('GET', '/api/search/{name}', 'App\Http\Api\Search\SeriesSearchController');
    $r->addRoute('GET', '/api/series/episodes/{id:\d+}', 'App\Http\Api\Series\EpisodesController');
});

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case \FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo('404 - Page not found');
        break;
    case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo('405 - Method not allowed');
        break;
    case \FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $presenter = new $handler();
        $presenter->setParams($vars, $em);
        $presenter->execute();

        //echo call_user_func_array($handler, $vars);

        break;
}

?>
