<?php

namespace GeekBrains\Project\Blog\Repositories\LikesRepository;

use PDO;
use GeekBrains\Project\Blog\Like;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Exceptions\LikeAlreadyExists;
use GeekBrains\Project\Blog\Exceptions\LikeNotFoundException;

class SqliteLikesRepository implements LikesRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Like $like): void
    {
        $statement = $this->connection->prepare('
            INSERT INTO likes (uuid, user_uuid, post_uuid)
            VALUES (:uuid, :user_uuid, :post_uuid)
        ');

        $statement->execute([
            ':uuid' => (string)$like->uuid(),
            ':user_uuid' => (string)$like->userUuid(),
            ':post_uuid' => (string)$like->postUuid(),
        ]);
    }

    /**
     * @throws LikesNotFoundException
     * @throws InvalidArgumentException
     */
    public function getByPostUuid(UUID $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :uuid'
        );

        $statement->execute([
            'uuid' => (string)$uuid
        ]);

        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        if (!$result) {
            throw new LikeNotFoundException(
                'No likes to post with uuid = : ' . $uuid
            );
        }

        $likes = [];
        foreach ($result as $like) {
            $likes[] = new Like(
                uuid: new UUID($like['uuid']),
                post_uuid: new UUID($like['post_uuid']),
                user_uuid: new UUID($like['user_uuid']),
            );
        }

        return $likes;
    }

    /**
     * @throws LikeAlreadyExists
     */
    public function checkUserLikeForPostExists($postUuid, $userUuid): void
    {
        $statement = $this->connection->prepare(
            'SELECT *
            FROM likes
            WHERE 
                post_uuid = :postUuid AND user_uuid = :userUuid'
        );

        $statement->execute(
            [
                ':postUuid' => $postUuid,
                ':userUuid' => $userUuid
            ]
        );

        $isExisted = $statement->fetch();

        if ($isExisted) {
            throw new LikeAlreadyExists(
                'The users like for this post already exists'
            );
        }
    }
}
