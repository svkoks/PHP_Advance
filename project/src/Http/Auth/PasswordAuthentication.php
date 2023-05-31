<?php

namespace GeekBrains\Project\Http\Auth;

use GeekBrains\Project\Blog\User;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Blog\Exceptions\AuthException;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Http\Auth\AuthenticationInterface;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class PasswordAuthentication implements AuthenticationInterface
{
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
        //
    }


    public function user(Request $request): User
    {
        try {
            $username = $request->jsonBodyField('username');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }
        try {
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            throw new AuthException($e->getMessage());
        }


        try {
            $password = $request->jsonBodyField('password');
        } catch (HttpException $e) {
            throw new AuthException($e->getMessage());
        }


        if (!$user->checkPassword($password)) {
            throw new AuthException('Wrong password');
        }

        return $user;
    }
}
