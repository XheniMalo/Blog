<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/Router.php';
require_once __DIR__ . '/middlewares/LoginMiddleware.php';
require_once __DIR__ . '/middlewares/AuthenticationMiddleware.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

Router::addMiddleware('LoginMiddleware');
Router::addMiddleware('AuthenticationMiddleware');
Router::route($method, $uri);

?>
