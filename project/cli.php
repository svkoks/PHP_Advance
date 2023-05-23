<?php

use Psr\Log\LoggerInterface;
use GeekBrains\Project\Blog\Commands\Arguments;
use GeekBrains\Project\Blog\Exceptions\AppException;
use GeekBrains\Project\Blog\Commands\CreateUserCommand;

$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
}
