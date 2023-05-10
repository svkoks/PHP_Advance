<?php

namespace GeekBrains\Project\Blog;

use GeekBrains\Project\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;

    public function __construct(UUID $uuid, Name $name, string $login)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $login;
    }

    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name и логином $this->username." . PHP_EOL;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function setName(Name $name): User
    {
        $this->name = $name;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->username;
    }

    public function setLogin(string $username): User
    {
        $this->username = $username;

        return $this;
    }
}
