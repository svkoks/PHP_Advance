<?php

require_once __DIR__ . '/vendor/autoload.php';

use GeekBrains\Project\Person\{Name};
use GeekBrains\Project\Blog\Commands\Arguments;
use GeekBrains\Project\Blog\Exceptions\AppException;
use GeekBrains\Project\Blog\Commands\CreateUserCommand;
use GeekBrains\Project\Blog\{Comment, Post, User, UUID};
use GeekBrains\Project\Blog\Repositories\CommentsRepository\SqliteCommentsRepository;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\Project\Blog\Repositories\UsersReposirory\InMemoryUsersRepository;



$connection = new PDO('sqlite:' . __DIR__ . '/blog.sqlite');

$usersRepository = new SqliteUsersRepository($connection);
$postsRepository = new SqlitePostsRepository($connection);
$commentsRepository = new SqliteCommentsRepository($connection);


try {
    $user = $usersRepository->get(new UUID('81871f1a-02dc-4936-b7b5-5d10984d6f8c'));

    // $post = new Post(
    //     UUID::random(),
    //     $user,
    //     'Header of post',
    //     'Posts text'
    // );
    // $postsRepository->save($post);
    $post = $postsRepository->get(new UUID('c895e60c-dc21-434f-96cc-c96f8ab55b2a'));
    //print_r($post);

    $comment = new Comment(
        UUID::random(),
        $post,
        $user,
        'Comments text'
    );
    $commentsRepository->save($comment);
    $comment = $commentsRepository->get(new UUID('3096f900-694c-4a10-a3b6-8cbea3f5980a'));
    //print_r($comment);
} catch (Exception $e) {
    echo $e->getMessage();
}


//$usersRepository = new InMemoryUsersRepository($connection);
//$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), 'admin'));
//$usersRepository->save(new User(UUID::random(), new Name('Anna', 'Petrova'), 'user'));



//$command = new CreateUserCommand($usersRepository);

// try {
//     //$usersRepository->save(new User(UUID::random(), new Name('Ivan', 'Nikitin'), 'admin'));
//     //echo $usersRepository->getByUsername('admin');

//     $command->handle(Arguments::fromArgv($argv));
// } catch (AppException $e) {
//     echo $e->getMessage();
// }
