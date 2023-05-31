<?php

namespace GeekBrains\Project\Http\Actions\Auth;

use DateTimeImmutable;
use GeekBrains\Project\Http\Request;
use GeekBrains\Project\Http\Response;
use GeekBrains\Project\Http\SuccessfulResponse;
use GeekBrains\Project\Http\Actions\ActionInterface;
use GeekBrains\Project\Blog\Exceptions\AuthException;
use GeekBrains\Project\Http\Auth\BearerTokenAuthentication;
use GeekBrains\Project\Blog\Exceptions\AuthTokenNotFoundException;
use GeekBrains\Project\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;

class LogOut implements ActionInterface
{
    public function __construct(
        private AuthTokensRepositoryInterface $authTokensRepository,
        private BearerTokenAuthentication $authentication
    ) {
    }

    /**
     * @throws AuthException
     */
    public function handle(Request $request): Response
    {
        $token = $this->authentication->getAuthTokenString($request);

        try {
            $authToken = $this->authTokensRepository->get($token);
        } catch (AuthTokenNotFoundException $exception) {
            throw new AuthException($exception->getMessage());
        }

        $authToken->setExpiresOn(new DateTimeImmutable("now"));


        $this->authTokensRepository->save($authToken);

        return new SuccessfulResponse([
            'token' => $authToken->token()
        ]);
    }
}
