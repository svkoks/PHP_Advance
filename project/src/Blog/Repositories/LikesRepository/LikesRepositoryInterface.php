<?php

namespace GeekBrains\Project\Blog\Repositories\LikesRepository;

use GeekBrains\Project\Blog\Like;
use GeekBrains\Project\Blog\UUID;

interface LikesRepositoryInterface
{
    public function save(Like $like): void;
    public function getByPostUuid(UUID $uuid): array;
    public function checkUserLikeForPostExists(UUID $postUuid, UUID $userUuid): void;
}
