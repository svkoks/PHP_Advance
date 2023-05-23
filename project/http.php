<?php

use Psr\Log\LoggerInterface;
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

$logger = $container->get(LoggerInterface::class);

try {
    $path = $request->path();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

try {
    $method = $request->method();
} catch (HttpException $e) {
    $logger->warning($e->getMessage());
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

if (
    !array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])
) {
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}

$actionClassName = $routes[$method][$path];

try {
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse)->send();
    return;
}
$response->send();
