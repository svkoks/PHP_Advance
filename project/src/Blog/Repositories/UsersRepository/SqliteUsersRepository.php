<?php

namespace GeekBrains\Project\Blog\Repositories\UsersRepository;

use PDO;
use PDOStatement;
use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;

use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class SqliteUsersRepository implements UsersRepositoryInterface
{
    private PDO $connection;
    private LoggerInterface $logger;

    public function __construct(PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        //$this->logger = $logger;
    }

    public function save(User $user): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO users (first_name, last_name, uuid, username,  password) VALUES (:first_name, :last_name, :uuid, :username, :password)
            ON CONFLICT (uuid) DO UPDATE SET
            first_name = :first_name,
            last_name = :last_name'
        );

        $statement->execute([
            ':first_name' => $user->getName()->getFirst(),
            ':last_name' => $user->getName()->getLast(),
            ':uuid' => (string)$user->uuid(),
            ':username' => $user->getLogin(),
            ':password' => $user->hashedPassword(),
        ]);

        //$this->logger->info("User created successfully: {$user->uuid()}");
    }


    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = ?'
        );
        $statement->execute([(string)$uuid]);

        return $this->getUser($statement, $uuid);
    }


    public function getByUsername(string $username): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );
        $statement->execute([
            ':username' => $username,
        ]);
        return $this->getUser($statement, $username);
    }

    private function getUser(PDOStatement $statement, string $strError): User
    {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $strError"
            );
        }

        return new User(
            new UUID($result['uuid']),
            new Name($result['first_name'], $result['last_name']),
            $result['username'],
            $result['password']
        );
    }
}
