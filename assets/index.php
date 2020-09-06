<?php

require __DIR__ . "/../vendor/autoload.php";

use App\Controller as Controller;
use App\Services as Services;

$router = new AltoRouter();

$router->map('GET', '/', function () {
    Controller\IndexController::renderMainView();
}, '/');

$router->map('GET', '/login', function () {
    Controller\LoginController::renderMainView();
}, 'login');

$router->map('GET', '/logout', function () {
    Services\Session::logout();
}, 'logout');

$router->map('GET', '/profile', function () {
    Controller\ProfileController::renderMainView();
}, 'profile');

$router->map('GET', '/purchase', function () {
    Controller\PurchaseController::renderMainView();
}, 'purchase');

$router->map('GET', '/register', function () {
    Controller\RegisterController::renderMainView();
}, 'register');

$router->map('GET', '/reset', function () {
    Controller\ResetController::renderMainView();
}, 'reset');

$router->map('POST', '/forms-processing', function () {
    switch ($_POST['action']) {
        case "register":
            Services\FormAction::registerForm($_POST);
            break;
        case "login":
            Services\FormAction::loginForm($_POST);
            break;
        case "profileUpdate":
            Services\FormAction::profileForm($_POST);
            break;
        case "forgottenPassword":
            Services\FormAction::forgottenPassword($_POST);
            break;
    }
}, 'forms-processing');

$match = $router->match();

//var_dump($router, $match);


if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    // no route matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}
