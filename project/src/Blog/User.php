<?php

namespace GeekBrains\Project\Blog;

use GeekBrains\Project\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $hashedPassword;

    public function __construct(UUID $uuid, Name $name, string $login, string $hashedPassword)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $login;
        $this->hashedPassword = $hashedPassword;
    }

    // public function __toString(): string
    // {
    //     return "Юзер $this->uuid с именем $this->name и логином $this->username." . PHP_EOL;
    // }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256',  $uuid . $password);
    }

    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    public static function createFrom(
        Name   $name,
        string $username,
        string $password
    ): self {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password, $uuid),
        );
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
