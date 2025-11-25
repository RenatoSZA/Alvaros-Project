<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('display_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Rio_branco');

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = rtrim(str_replace('\\', '/', $scriptDir), '/');
define('BASE_URL', $baseUrl);

spl_autoload_register(function ($class) {

    $baseDir = __DIR__ . '/../src/';

    if (strpos($class, 'Core\\') === 0) {

        $relativeClass = str_replace('Core\\', '', $class);
        $file = $baseDir . 'Core/' . str_replace('\\', '/', $relativeClass) . '.php';
    } 

    elseif (strpos($class, 'App\\') === 0) {

        $relativeClass = str_replace('App\\', '', $class);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    }

    if (isset($file) && file_exists($file)) {
        require $file;
    }
});

use Core\Router;

if (!class_exists('Core\Router')) {
    die("ERRO FATAL: O arquivo src/Core/Router.php não foi encontrado ou o namespace está errado.");
}

$router = new Router();

$router->get('/', function() { 
    require __DIR__ . '/../views/home.php'; 
});

$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@processLogin');

$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@processRegister');

$router->get('/logout', 'AuthController@logout');

$router->get('/dashboard', 'DashboardController@index');

$router->get('/api/aluno-data', 'DashboardController@getStudentData');

$router->dispatch();
