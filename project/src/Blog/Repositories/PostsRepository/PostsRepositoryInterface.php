<?php

namespace GeekBrains\Project\Blog\Repositories\PostsRepository;

use GeekBrains\Project\Blog\Post;
use GeekBrains\Project\Blog\UUID;

interface PostsRepositoryInterface
{
    public function save(Post $post): void;
    public function get(UUID $uuid): Post;
    //public function delete(UUID $uuid): void;
}
