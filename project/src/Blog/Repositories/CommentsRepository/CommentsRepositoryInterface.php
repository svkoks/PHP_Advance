<?php

namespace GeekBrains\Project\Blog\Repositories\CommentsRepository;

use GeekBrains\Project\Blog\UUID;
use GeekBrains\Project\Blog\Comment;

interface CommentsRepositoryInterface
{
    public function save(Comment $text): void;
    public function get(UUID $uuid): Comment;
    //public function delete(UUID $uuid): void;
}
