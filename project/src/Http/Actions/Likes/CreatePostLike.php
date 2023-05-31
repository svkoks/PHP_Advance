<?php

namespace GeekBrains\Project\Http\Actions\Likes;

use InvalidArgumentException;
use GeekBrains\Project\Blog\Like;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Blog\Exceptions\LikeAlreadyExists;
use GeekBrains\Project\Blog\Exceptions\PostNotFoundException;
use GeekBrains\Project\Blog\Repositories\LikesRepository\LikesRepositoryInterface;
use GeekBrains\Project\Blog\Repositories\PostsRepository\PostsRepositoryInterface;

class CreatePostLike implements ActionInterface
{
    public   function __construct(
        private LikesRepositoryInterface $likesRepository,
        private PostsRepositoryInterface $postRepository,
        private TokenAuthenticationInterface $authentication
    ) {
    }


    /**
     * @throws InvalidArgumentException
     */
    public function handle(Request $request): Response
    {
        try {
            $author = $this->authentication->user($request);
        } catch (AuthException $exception) {
            return new ErrorResponse($exception->getMessage());
        }
        
        try {
            $postUuid = $request->JsonBodyField('post_uuid');
        } catch (HttpException $e) {
            return new ErrorResponse($e->getMessage());
        }


        try {
            $this->postRepository->get(new UUID($postUuid));
        } catch (PostNotFoundException $exception) {
            return new ErrorResponse($exception->getMessage());
        }

        try {
            $this->likesRepository->checkUserLikeForPostExists($postUuid, $author->uuid());
        } catch (LikeAlreadyExists $e) {
            return new ErrorResponse($e->getMessage());
        }

        $newLikeUuid = UUID::random();

        $like = new Like(
            uuid: $newLikeUuid,
            post_uuid: new UUID($postUuid),
            user_uuid: $author->uuid(),

        );

        $this->likesRepository->save($like);

        return new SuccessfulResponse(
            ['uuid' => (string)$newLikeUuid]
        );
    }
}
