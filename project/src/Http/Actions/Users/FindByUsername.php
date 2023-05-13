<?php

namespace GeekBrains\Project\Http\Actions\Users;

use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\HttpException;
use GeekBrains\Project\Blog\Exсeptions\UserNotFoundException;
use GeekBrains\Project\Blog\Repositories\UsersRepository\UsersRepositoryInterface;

class FindByUsername implements ActionInterface
{
    // Нам понадобится репозиторий пользователей,
    // внедряем его контракт в качестве зависимости
    public function __construct(
        private UsersRepositoryInterface $usersRepository
    ) {
    }



    public function handle(Request $request): Response
    {
        try {
            // Пытаемся получить искомое имя пользователя из запроса
            $username = $request->query('username');
        } catch (HttpException $e) {
            // Если в запросе нет параметра username -
            // возвращаем неуспешный ответ,
            // сообщение об ошибке берём из описания исключения
            return new ErrorResponse($e->getMessage());
        }


        try {
            // Пытаемся найти пользователя в репозитории
            $user = $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException $e) {
            // Если пользователь не найден -
            // возвращаем неуспешный ответ
            return new ErrorResponse($e->getMessage());
        }


        // Возвращаем успешный ответ
        return new SuccessfulResponse([
            'username' => $user->getLogin(),
            'name' => $user->getName()->getFirst() . ' ' . $user->getName()->getLast(),
        ]);
    }
}
