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
     * プロパイダ―IDを基にしてDBからユーザーレコードを取得する
     */
    public function fetchProviderId(string $providerId): User
    {

        $userRecord = $this->authRepository->findUserRecordByProviderId($providerId);

        return new User(
            userId: $userRecord['id'],
            email: $userRecord['email'],
            isNewUser: false,
            providerId: $providerId,
            providerName: 'Google'
        );
    }

    /**
     * 登録済みのプロパイダ―がDBのレコードに存在するか確認する
     */
    public function providerRecordExists(string $providerId): bool
    {
        return $this->authRepository->providerIdExists($providerId);
    }
}