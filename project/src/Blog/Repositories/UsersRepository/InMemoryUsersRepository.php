<?php

namespace GeekBrains\Project\Blog\Repositories\UsersReposirory;

use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class InMemoryUsersRepository implements UsersRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function get(UUID $uuid): User
    {
        foreach ($this->users as $user) {
            if ((string)$user->uuid() === (string)$uuid) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $uuid");
    }

    public function getByUsername(string $username): User
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        throw new UserNotFoundException("User not found: $username");
    }
}
