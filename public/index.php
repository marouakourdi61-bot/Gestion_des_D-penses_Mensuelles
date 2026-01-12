<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/Router.php';

use Core\Router;

echo "Index works<br>";

$router = new Router();
$router->run();

