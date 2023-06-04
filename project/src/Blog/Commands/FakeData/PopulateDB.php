<?php

namespace GeekBrains\Project\Blog\Commands\FakeData;

use Faker\Generator;
use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;
use GeekBrains\Project\Blog\Comment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;

class PopulateDB extends Command
{
    public function __construct(
        private Generator $faker,
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository,
        private CommentsRepositoryInterface $commentsRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('fake-data:populate-db')
            ->setDescription('Populates DB with fake data')
            ->addArgument(
                'users-number',
                InputArgument::REQUIRED,
                'Users number'
            )
            ->addArgument(
                'posts-number',
                InputArgument::REQUIRED,
                'Posts number'
            )
            ->addArgument(
                'comments-number',
                InputArgument::REQUIRED,
                'Comments number'
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        // Создаём десять пользователей
        $users = [];
        $posts = [];
        $users_number = $input->getArgument('users-number');

        for ($i = 0; $i < $users_number; $i++) {
            $user = $this->createFakeUser();
            $users[] = $user;
            $output->writeln('User created: ' . $user->getLogin());
        }

        // От имени каждого пользователя
        // создаём по двадцать статей
        $posts_number = $input->getArgument('posts-number');
        foreach ($users as $user) {
            for ($i = 0; $i < $posts_number; $i++) {
                $post = $this->createFakePost($user);
                $output->writeln('Post created: ' . $post->getTitle());
            }
        }
        $comments_number = $input->getArgument('comments-number');
        foreach ($posts as $post) {
            for ($i = 0; $i < $comments_number; $i++) {
                $comment = $this->createFakeComment($post, $users[array_rand($users)]);
                $output->writeln('Comment created: ' . $comment->getText());
            }
        }

        return Command::SUCCESS;
    }


    private function createFakeComment(Post $post, User $author): Comment
    {
        $comment = new Comment(
            UUID::random(),
            $post,
            $author,
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->commentsRepository->save($comment);
        return $comment;
    }


    private function createFakeUser(): User
    {
        $user = User::createFrom(
            // Генерируем имя пользователя
            $this->faker->username,
            new Name(
                // Генерируем имя
                $this->faker->firstName,
                // Генерируем фамилию
                $this->faker->lastName
            ),
            // Генерируем пароль
            $this->faker->password,
        );

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save($user);
        return $user;
    }

    private function createFakePost(User $author): Post
    {
        $post = new Post(
            UUID::random(),
            $author,
            // Генерируем предложение не длиннее шести слов
            $this->faker->sentence(6, true),
            // Генерируем текст
            $this->faker->realText
        );

        // Сохраняем статью в репозиторий
        $this->postsRepository->save($post);
        return $post;
    }
}
