<?php

namespace GeekBrains\Project\Blog;

class Post
{
    private UUID $uuid;
    private User $user;
    private string $title;
    private string $text;

    public function __construct(
        UUID $uuid,
        User $user,
        string $title,
        string $text
    ) {
        $this->uuid = $uuid;
        $this->user = $user;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString()
    {
        return $this->user . ' пишет пост: ' . $this->text . PHP_EOL;
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

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
}
