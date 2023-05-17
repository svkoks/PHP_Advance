<?php

use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Blog\Exceptions\AppException;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Http\Actions\Posts\CreatePost;
use GeekBrains\Project\Http\Actions\Posts\DeletePost;
use GeekBrains\Project\Http\Actions\Users\CreateUser;
use GeekBrains\Project\Http\Actions\Users\FindByUsername;

$container = require __DIR__ . '/bootstrap.php';

$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input'),
);

try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}


$routes = [
    'GET' => [
        '/users/show' => FindByUsername::class,
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePost::class,
        '/posts/likes/create' => CreatePostLike::class
    ],
    'DELETE' => [
        '/posts' => DeletePost::class,
    ],

];

if (!array_key_exists($method, $routes)) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse("Route not found: $method $path"))->send();
    return;
}

$actionClassName = $routes[$method][$path];

$action = $container->get($actionClassName);

try {
    $response = $action->handle($request);
} catch (AppException $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
$response->send();
