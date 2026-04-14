<?php
declare(strict_types=1);

namespace Service;

use Repository\UserAuthRepository;
use Entity\User;

class GoogleUserService
{

    public function __construct(private UserAuthRepository $authRepository)
    {
    }

    /**
     * ユーザーデータをDBのレコードと同期する
     */
    public function synchronizeUserData(
        string $providerId,
        string $email,
        ?string $refreshToken = null
    ): User {
        if ($this->authRepository->providerIdExists($providerId)) {

            $userRecord = $this->authRepository->findUserRecordByProviderId($providerId);

            return new User(
                userId: $userRecord['id'],
                email: $userRecord['email'],
                isNewUser: false,
                providerId: $providerId,
                providerName: 'Google',
                refreshToken: $refreshToken
            );
        }

        $userRecord = $this->authRepository->fetchNewUserRecord($email);
        $this->authRepository->linkProviderUserId($userRecord['id'], $providerId, 'Google', $refreshToken);

        return new User(
            userId: $userRecord['id'],
            email: $userRecord['email'],
            isNewUser: true,
            providerId: $providerId,
            providerName: 'Google',
            refreshToken: $refreshToken
        );
    }
}