<?php

namespace GeekBrains\Project\Blog;

class Comment
{
    private UUID $uuid;
    private Post $post;
    private User $user;
    private string $text;

    public function __construct(UUID $uuid, Post $post, User $user, string $text)
    {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->post = $post;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->user . ' пишет комментарий: ' . $this->text . PHP_EOL;
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function setUuid(UUID $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function setPost(Post $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
