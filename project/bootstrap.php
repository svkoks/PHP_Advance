<?php

use PDO;
use GeekBrains\Project\Blog\Container\DIContainer;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
);

$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostsRepository::class
);

$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);

$container->bind(
    LikesRepositoryInterface::class,
    SqliteLikesRepository::class
);

return $container;
