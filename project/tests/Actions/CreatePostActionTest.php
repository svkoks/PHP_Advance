<?php

namespace GeekBrains\Project\tests\Actions;


use PHPUnit\Framework\TestCase;
use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\tests\DummyLogger;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Blog\Exceptions\AuthException;
use GeekBrains\Project\Blog\Exceptions\JsonException;
use GeekBrains\Project\Http\Actions\Posts\CreatePost;
use GeekBrains\Project\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Http\Auth\JsonBodyUsernameIdentification;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class CreatePostActionTest extends TestCase
{
    private function postsRepository(): PostsRepositoryInterface
    {
        return new class() implements PostsRepositoryInterface
        {
            private bool $called = false;

            public function __construct()
            {
            }

            public function save(Post $post): void
            {
                $this->called = true;
            }

            public function get(UUID $uuid): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getByTitle(string $title): Post
            {
                throw new PostNotFoundException('Not found');
            }

            public function getCalled(): bool
            {
                return $this->called;
            }

            public function delete(UUID $uuid): void
            {
            }
        };
    }

    private function usersRepository(array $users): UsersRepositoryInterface
    {
        return new class($users) implements UsersRepositoryInterface
        {
            public function __construct(
                private array $users
            ) {
            }

            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                foreach ($this->users as $user) {
                    if ($user instanceof User && (string)$uuid == $user->uuid()) {
                        return $user;
                    }
                }
                throw new UserNotFoundException('Cannot find user: ' . $uuid);
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException('Not found');
            }
        };
    }


    public function testItReturnsSuccessAnswer(): void
    {
        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyUsernameIdentification::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID("81871f1a-02dc-4936-b7b5-5d10984d6f8c"),
                    new Name('firstName', 'lastName'),
                    'username',
                )
            );

        $createPost = new CreatePost(
            $postsRepositoryStub,
            new DummyLogger(),
            $authenticationStub
        );

        $request = new Request(
            [],
            [],
            '{
                "title": "Header of post",
                "text": "Posts text"
                }'
        );

        $actual = $createPost->handle($request);

        $this->assertInstanceOf(
            SuccessFulResponse::class,
            $actual
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsSuccessfulResponse(): void
    {
        $request = new Request([], [], '{"author_uuid":"81871f1a-02dc-4936-b7b5-5d10984d6f8c","title":"Header of post","text":"Posts text"}');

        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);

        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID('81871f1a-02dc-4936-b7b5-5d10984d6f8c'),
                    new Name('firstName', 'lastName'),
                    'username',
                )
            );

        $postsRepository = $this->postsRepository();

        $action = new CreatePost($postsRepository, new DummyLogger(), $authenticationStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(SuccessfulResponse::class, $response);

        $this->setOutputCallback(function ($data) {
            $dataDecode = json_decode(
                $data,
                associative: true,
                flags: JSON_THROW_ON_ERROR
            );

            $dataDecode['data']['uuid'] = "351739ab-fc33-49ae-a62d-b606b7038c87";
            return json_encode(
                $dataDecode,
                JSON_THROW_ON_ERROR
            );
        });

        $this->expectOutputString('{"success":true,"data":{"uuid":"351739ab-fc33-49ae-a62d-b606b7038c87"}}');


        $response->send();
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testItReturnsErrorResponseIfNotFoundUser(): void
    {
        $request = new Request([], [], '{"author_uuid":"81871f1a-02dc-4936-b7b5-5d10984d6f8c","title":"Header of post","text":"Posts text"}');

        $postsRepositoryStub = $this->createStub(PostsRepositoryInterface::class);
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);

        $authenticationStub
            ->method('user')
            ->willThrowException(
                new AuthException('Cannot find user: 81871f1a-02dc-4936-b7b5-5d10984d6f8c')
            );


        $action = new CreatePost($postsRepositoryStub, new DummyLogger(), $authenticationStub);

        $response = $action->handle($request);
        $response->send();

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"Cannot find user: 81871f1a-02dc-4936-b7b5-5d10984d6f8c"}');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @throws JsonException
     */
    public function testItReturnsErrorResponseIfNoTextProvided(): void
    {
        $request = new Request([], [], '{"author_uuid":"81871f1a-02dc-4936-b7b5-5d10984d6f8c","title":"Header of post"}');

        $postsRepository = $this->postsRepository([]);
        $authenticationStub = $this->createStub(JsonBodyUuidIdentification::class);
        $authenticationStub
            ->method('user')
            ->willReturn(
                new User(
                    new UUID('81871f1a-02dc-4936-b7b5-5d10984d6f8c'),
                    new Name('firstName', 'lastName'),
                    'username',
                )
            );

        $action = new CreatePost($postsRepository, new DummyLogger(), $authenticationStub);

        $response = $action->handle($request);

        $this->assertInstanceOf(ErrorResponse::class, $response);
        $this->expectOutputString('{"success":false,"reason":"No such field: text"}');

        $response->send();
    }
}
