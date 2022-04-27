<?php

use App\Controller\{ArticleController, SigninController, SignoutController, SignupController, UserController};

$router->map('GET', '/', function() {
    $articleController = new ArticleController();
    $articleController->index();
});
$router->map('POST', '/article/new', function() {
    $articleController = new ArticleController();
    $articleController->new();
});
$router->map('GET', '/article/show', function() {
    $articleController = new ArticleController();
    $articleController->show();
});
$router->map('PUT', '/article/edit', function() {
    $articleController = new ArticleController();
    $articleController->edit();
});
$router->map('DELETE', '/article/delete', function() {
    $articleController = new ArticleController();
    $articleController->delete();
});
$router->map('POST', '/signup', function () {
    $signupController = new SignupController();
    $signupController->index();
});
$router->map('GET|POST', '/signin', function () {
    $signinController = new SigninController();
    $signinController->index();
});
$router->map('GET|POST', '/signout', function () {
    $signoutController = new SignoutController();
    $signoutController->index();
});
$router->map('GET','/user',function(){
    $userController = new UserController();
    $userController->index();
});
$router->map('GET','/user/show',function(){
    $userController = new UserController();
    $userController->userShow();
});
$router->map('DELETE','/user/delete',function(){
    $userController = new UserController();
    $userController->delete();
});
$router->map('GET|PUT','/user/edit',function(){
    $userController = new UserController();
    $userController->edit();
});
