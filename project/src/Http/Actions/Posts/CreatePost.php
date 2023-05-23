<?php

namespace GeekBrains\Project\Http\Actions\Posts;

use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\AuthException;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Http\Auth\IdentificationInterface;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;


class CreatePost implements ActionInterface
{
    public function __construct(
        private PostsRepositoryInterface $postsRepository,
        private LoggerInterface $logger,
        private IdentificationInterface $identification
    ) {
    }

    public function handle(Request $request): Response
    {
        // try {
        //     $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        // } catch (HttpException | InvalidArgumentException $e) {
        //     return new ErrorResponse($e->getMessage());
        // }

        // try {
        //     $user = $this->usersRepository->get($authorUuid);
        // } catch (UserNotFoundException $e) {
        //     return new ErrorResponse($e->getMessage());
        // }

        try {
            $user = $this->identification->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $this->postsRepository->save($post);

        $this->logger->info("Post created: $newPostUuid");

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
