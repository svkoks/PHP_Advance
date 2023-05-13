<?php

namespace GeekBrains\Project\Http\Actions\Posts;

use InvalidArgumentException;
use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class CreatePost implements ActionInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private PostsRepositoryInterface $postsRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $newPostUuid = UUID::random();

        try {
            $post = new Post(
                $newPostUuid,
                $user,
                $request->jsonBodyField('title'),
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        $this->postsRepository->save($post);

        return new SuccessfulResponse([
            'uuid' => (string)$newPostUuid,
        ]);
    }
}
