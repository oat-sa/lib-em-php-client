<?php

declare(strict_types=1);

namespace OAT\Library\EnvironmentManagementClient\Tests\Traits;

use Carbon\Carbon;
use DateTimeImmutable;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Token;

trait EnvironmentManagementTokenTestingTrait
{
    /**
     * Returns an unsigned access token identical to the access token issued by the Environment Management's Auth Server
     * @see https://github.com/oat-sa/environment-management/blob/develop/auth-server/src/Oauth2/Entity/AccessToken.php
     *
     * @param string $identifier
     * @param string $keyChainIdentifier
     * @param string $userIdentifier
     * @param string $issuer
     * @param array $scopes
     * @param string $tenantId
     * @param DateTimeImmutable|null $expiryDateTime
     * @param DateTimeImmutable|null $nowDateTime
     * @return Token
     */
    public function buildAuthServerAccessToken(
        string $identifier = 'client_id',
        string $keyChainIdentifier = 'key_chain_id',
        string $userIdentifier = 'user_id',
        string $issuer = 'issuer',
        array $scopes = [],
        string $tenantId = 'tenant_id',
        DateTimeImmutable $expiryDateTime = null,
        DateTimeImmutable $nowDateTime = null,
    ): Token {
        $configuration = Configuration::forUnsecuredSigner();

        if ($nowDateTime === null) {
            $nowDateTime = Carbon::now()->toImmutable();
        }

        if ($expiryDateTime === null) {
            $expiryDateTime = Carbon::createFromImmutable($now)->addHour()->toImmutable();
        }

        return $configuration->builder()
            ->identifiedBy($identifier)
            ->withHeader('kid', $keyChainIdentifier)
            ->relatedTo($userIdentifier)
            ->issuedBy($issuer)
            ->expiresAt($expiryDateTime)
            ->issuedAt($nowDateTime)
            ->canOnlyBeUsedAfter($nowDateTime)
            ->withClaim('scopes', $scopes)
            ->withClaim('tenant_id', $tenantId)
            ->getToken($configuration->signer(), $configuration->signingKey());
    }

    /**
     * Returns a refresh token identical to the refresh token issued by the Environment Management's Auth Server
     * @see https://github.com/oat-sa/environment-management/blob/develop/auth-server/src/Oauth2/Entity/RefreshToken.php
     *
     * @param string $identifier
     * @param string $keyChainIdentifier
     * @param string $userIdentifier
     * @param string $issuer
     * @param array $scopes
     * @param string $tenantId
     * @param DateTimeImmutable|null $expiryDateTime
     * @param DateTimeImmutable|null $nowDateTime
     * @return Token
     */
    public function buildAuthServerRefreshToken(
        string $identifier = 'client_id',
        string $keyChainIdentifier = 'key_chain_id',
        string $userIdentifier = 'user_id',
        string $issuer = 'issuer',
        array $scopes = [],
        string $tenantId = 'tenant_id',
        DateTimeImmutable $expiryDateTime = null,
        DateTimeImmutable $nowDateTime = null,
    ): Token {
        // At the moment we are generating the refresh tokens on the same way as the access tokens
        return $this->buildAuthServerAccessToken(
            $identifier,
            $keyChainIdentifier,
            $userIdentifier,
            $issuer,
            $scopes,
            $tenantId,
            $expiryDateTime,
            $nowDateTime,
        );
    }
}
