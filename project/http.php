<?php

use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Blog\Exceptions\AppException;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Http\Actions\Posts\CreatePost;
use GeekBrains\Project\Http\Actions\Posts\DeletePost;
use GeekBrains\Project\Http\Actions\Users\CreateUser;
use GeekBrains\Project\Http\Actions\Users\FindByUsername;
use GeekBrains\Project\Http\Actions\Comments\CreateComment;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Project\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;

require_once __DIR__ . '/vendor/autoload.php';

$request = new Request($_GET, $_SERVER, file_get_contents('php://input'),);

$routes = [
    'GET' => [
        '/users/show' => new FindByUsername(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        '/users/create' => new CreateUser(
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/posts/create' => new CreatePost(
            new SqliteUsersRepository(
                new PDO('sqlite:', __DIR__, '/blog.sqlite')
            ),
            new SqlitePostsRepository(
                new PDO('sqlite:', __DIR__, '/blog.sqlite')
            )
        ),
        '/posts/comment' => new CreateComment(
            new SqliteCommentsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'DELETE' => [
        '/posts' => new DeletePost(
            new SqlitePostsRepository(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],

];


try {
    $path = $request->path();
} catch (HttpException) {
    (new ErrorResponse)->send();
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    (new ErrorResponse)->send();
    return;
}

// Если у нас нет маршрутов для метода запроса -
// возвращаем неуспешный ответ
if (!array_key_exists($method, $routes)) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем действие по методу и пути
$action = $routes[$method][$path];

try {
    $response = $action->handle($request);
    $response->send();
} catch (Exception $e) {
    (new ErrorResponse($e->getMessage()))->send();
}
