<?php

namespace GeekBrains\Project\Http\Actions\Comments;

use InvalidArgumentException;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Comment;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\CommentsRepository\CommentsRepositoryInterface;


class CreateComment implements ActionInterface
{
    public function __construct(
        private CommentsRepositoryInterface $commentsRepository,
        private PostsRepositoryInterface $postsRepository,
        private UsersRepositoryInterface $usersRepository,
    ) {
    }
    public function handle(Request $request): Response
    {
        try {
            $authorUuid = new UUID($request->jsonBodyField('author_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }
        try {
            $user = $this->usersRepository->get($authorUuid);
        } catch (UserNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $postUuid = new UUID($request->jsonBodyField('post_uuid'));
        } catch (HttpException | InvalidArgumentException $e) {
            return new ErrorResponse($e->getMessage());
        }

        try {
            $post = $this->postsRepository->get($postUuid);
        } catch (PostNotFoundException $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newCommentUuid = UUID::random();
        try {
            $comment = new Comment(
                $newCommentUuid,
                $post,
                $user,
                $request->jsonBodyField('text'),
            );
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }
        $this->commentsRepository->save($comment);
        return new SuccessfulResponse([
            'uuid' => (string)$newCommentUuid,
        ]);
    }
}
