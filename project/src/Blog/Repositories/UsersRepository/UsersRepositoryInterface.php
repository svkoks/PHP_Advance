<?php

namespace GeekBrains\Project\Blog\Repositories\UsersRepository;

use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Blog\UUID;

interface UsersRepositoryInterface
{
    public function save(User $user): void;
    public function get(UUID $uuid): User;
    public function getByUsername(string $username): User;
}
