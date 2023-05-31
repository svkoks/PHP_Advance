<?php

namespace GeekBrains\Project\Http\Actions\Auth;

use DateTimeImmutable;
use GeekBrains\Project\Blog\AuthToken;
use GeekBrains\Project\Blog\Exceptions\AuthException;
use GeekBrains\Project\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\ErrorResponse;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\SuccessfulResponse;

class LogIn implements ActionInterface
{
    public function __construct(
        // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
        // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    public function handle(Request $request): Response
    {
        // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
// Генерируем токен
        $authToken = new AuthToken(
            // Случайная строка длиной 40 символов
            bin2hex(random_bytes(40)),
            $user->uuid(),
            // Срок годности - 1 день
            (new DateTimeImmutable())->modify('+1 day')
        );
        // Сохраняем токен в репозиторий
        $this->authTokensRepository->save($authToken);
        // Возвращаем токен
        return new SuccessfulResponse([
            'token' => $authToken->token(),
        ]);

    }
}
