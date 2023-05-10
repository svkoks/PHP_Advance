<?php

namespace GeekBrains\Project\tests;

use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;
use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;



class SqlitePostsRepositoryTest extends TestCase
{
    public function testItThrowsAnExceptionWhenPostNotFound(): void
    {
    }

    public function testItSavesPostToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);


        $repository = new SqlitePostsRepository($connectionStub);

        $user = new User(
            new UUID('81871f1a-02dc-4936-b7b5-5d10984d6f8c'),
            new Name('first_name', 'last_name'),
            'name',
        );

        $repository->save(
            new Post(
                new UUID('c895e60c-dc21-434f-96cc-c96f8ab55b2a'),
                $user,
                'Header of post',
                'Posts text'
            )
        );
    }

    public function testItGetPostByUuid(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock->method('fetch')->willReturn([
            'uuid' => 'c895e60c-dc21-434f-96cc-c96f8ab55b2a',
            'author_uuid' => '81871f1a-02dc-4936-b7b5-5d10984d6f8c',
            'title' => 'Header of post',
            'text' => 'Posts text',
            'username' => 'ivan123',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
        ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $postRepository = new SqlitePostsRepository($connectionStub);
        $post = $postRepository->get(new UUID('c895e60c-dc21-434f-96cc-c96f8ab55b2a'));

        $this->assertSame('c895e60c-dc21-434f-96cc-c96f8ab55b2a', (string)$post->getUuid());
    }
}
