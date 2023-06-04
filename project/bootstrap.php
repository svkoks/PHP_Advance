<?php

use PDO;
use Dotenv\Dotenv;
use Monolog\Logger;
use Faker\Generator;
use Faker\Provider\Lorem;
use Psr\Log\LoggerInterface;
use Faker\Provider\ru_RU\Text;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Internet;
use Monolog\Handler\StreamHandler;
use GeekBrains\Project\Blog\Container\DIContainer;
use GeekBrains\Project\Http\Auth\PasswordAuthentication;
use GeekBrains\Project\Http\Auth\AuthenticationInterface;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();


$container = new DIContainer();

$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ .  '/' . $_ENV['SQLITE_DB_PATH'])
);

// Выносим объект логгера в переменную
$logger = (new Logger('blog'));

// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_FILES']) {
    $logger
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}

// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_ENV['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}


// Создаём объект генератора тестовых данных
$faker = new Generator();
// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

// Добавляем генератор тестовых данных
// в контейнер внедрения зависимостей
$container->bind(
    Generator::class,
    $faker
);


$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);


$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);


$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

// $container->bind(
//     IdentificationInterface::class,
//     JsonBodyUuidIdentification::class
// );

$container->bind(
    IdentificationInterface::class,
    JsonBodyUsernameIdentification::class
);


$container->bind(
    LoggerInterface::class,
    $logger
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

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentsRepository::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);

return $container;
