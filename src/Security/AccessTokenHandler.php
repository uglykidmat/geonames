<?php
// src/Security/AccessTokenHandler.php
namespace App\Security;

use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;

class AccessTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private readonly string $token,
        private readonly string $user,
    ) {
    }

    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        if ($accessToken === $this->token) {
            return new UserBadge($this->user);
        }

        throw new BadCredentialsException('Invalid credentials.');
    }
}
