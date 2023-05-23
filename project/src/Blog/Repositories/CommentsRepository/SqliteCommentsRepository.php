<?php

namespace GeekBrains\Project\Blog\Repositories\CommentsRepository;

use PDO;
use PDOStatement;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Comment;
use GeekBrains\Project\Blog\Exceptions\CommentNotFoundException;
use GeekBrains\Project\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\Project\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use Psr\Log\LoggerInterface;

class SqliteCommentsRepository implements CommentsRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
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

        $this->logger->info("Comment created successfully: {$text->getUuid()}");
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
            $message = "Cannot find comment: $commentUuid";
            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }

        $usersRepository = new SqliteUsersRepository($this->connection, $this->logger);
        $user = $usersRepository->get(new UUID($result['author_uuid']));

        $postsRepository = new SqlitePostsRepository($this->connection, $this->logger);
        $post = $postsRepository->get(new UUID($result['post_uuid']));

        return new Comment(
            new UUID($result['uuid']),
            $post,
            $user,
            $result['text']
        );
    }
}
