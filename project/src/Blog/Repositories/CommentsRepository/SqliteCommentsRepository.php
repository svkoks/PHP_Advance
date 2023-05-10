<?php

namespace GeekBrains\Project\Blog\Repositories\CommentsRepository;

use PDO;
use PDOStatement;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Comment;
use GeekBrains\Project\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private PDO $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(Comment $text): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, author_uuid, text) VALUES (:uuid, :post_uuid, :author_uuid, :text)'
        );
        $statement->execute([
            ':uuid' => $text->getUuid(),
            ':post_uuid' => $text->getPost()->getUuid(),
            ':author_uuid' => $text->getUser()->uuid(),
            ':text' => $text->getText()
        ]);
    }

    public function get(UUID $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getComment($statement, $uuid);
    }

    private function getComment(PDOStatement $statement, string $commentUuid): Comment
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new CommentNotFoundException(
                "Cannot find comment: $commentUuid"
            );
        }

        $usersRepository = new SqliteUsersRepository($this->connection);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        $postsRepository = new SqlitePostsRepository($this->connection);
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }
}
