<?php

namespace GeekBrains\Project\Blog\Repositories\UsersReposirory;

use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Person\Name;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class DummyUsersRepository implements UsersRepositoryInterface
{
    public function save(User $user): void
    {
        //
    }

    public function get(UUID $uuid): User
    {
        throw new UserNotFoundException("Not found");
    }

    public function getByUsername(string $username): User
    {
        return new User(UUID::random(), new Name("firstName", "lastName"), "user123");
    }
}
