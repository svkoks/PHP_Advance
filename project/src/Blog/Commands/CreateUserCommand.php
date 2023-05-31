<?php

namespace GeekBrains\Project\Blog\Commands;

use Psr\Log\LoggerInterface;
use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;
use GeekBrains\Project\Blog\Exсeptions\CommandException;
use GeekBrains\Project\Blog\Exceptions\ArgumentsException;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Exсeptions\InvalidArgumentException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class CreateUserCommand
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws CommandException
     * @throws InvalidArgumentException|ArgumentsException
     */
    public function handle(Arguments $arguments): void
    {
        // Логируем информацию о том, что команда запущена
        // Уровень логирования – INFO
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');


        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            throw new CommandException("User already exists: $username");
            //return;
        }

        $user = User::createFrom(
            new Name(
                $arguments->get('first_name'),
                $arguments->get('last_name')
            ),
            $username,
            $arguments->get('password')
        );

        $this->usersRepository->save($user);

        // Логируем информацию о новом пользователе
        $this->logger->info("User created: " . $user->uuid());
    }

    private function userExists(string $username): bool
    {
        try {
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
