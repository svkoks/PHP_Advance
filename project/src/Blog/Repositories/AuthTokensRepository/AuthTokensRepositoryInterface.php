<?php

namespace GeekBrains\Project\Blog\Repositories\AuthTokensRepository;

use GeekBrains\Project\Blog\AuthToken;

interface AuthTokensRepositoryInterface
{
    // Метод сохранения токена
    public function save(AuthToken $authToken): void;

    // Метод получения токена
    public function get(string $token): AuthToken;
}
