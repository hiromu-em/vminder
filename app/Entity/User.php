<?php
declare(strict_types=1);

namespace Entity;

final class User
{
    public function __construct(
        private string $userId,
        private string $email,
        private bool $isNewUser,
        private ?string $providerId,
        private ?string $providerName,
        private ?string $refreshToken = null
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}