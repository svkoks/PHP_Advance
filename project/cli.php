<?php

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use GeekBrains\Project\Blog\Commands\Arguments;
use GeekBrains\Project\Blog\Exceptions\AppException;
use GeekBrains\Project\Blog\Commands\Posts\DeletePost;
use GeekBrains\Project\Blog\Commands\Users\CreateUser;
use GeekBrains\Project\Blog\Commands\Users\UpdateUser;
use GeekBrains\Project\Blog\Commands\CreateUserCommand;
use GeekBrains\Project\Blog\Commands\FakeData\PopulateDB;

$container = require __DIR__ . '/bootstrap.php';

// При помощи контейнера создаём команду
//$command = $container->get(CreateUserCommand::class);

// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

// Создаём объект приложения
$application = new Application();

// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,

];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

try {
    $application->run();
} catch (Exception $e) {
    $logger->error($e->getMessage(), ['exception' => $e]);
    echo $e->getMessage();
}


// try {
//     $command->handle(Arguments::fromArgv($argv));
// } catch (AppException $e) {
//     $logger->error($e->getMessage(), ['exception' => $e]);
// }
