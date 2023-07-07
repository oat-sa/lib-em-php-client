<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace OAT\Library\EnvironmentManagementClient\Grpc;

use Oat\Envmgmt\Common\Oauth2ClientSecret;
use Oat\Envmgmt\Common\Oauth2UserPassword;
use Oat\Envmgmt\Sidecar\GetClientRequest;
use Oat\Envmgmt\Sidecar\GetClientUserRequest;
use Oat\Envmgmt\Sidecar\Oauth2ClientServiceClient;
use Oat\Envmgmt\Sidecar\ValidateClientSecretRequest;
use Oat\Envmgmt\Sidecar\ValidateUserPasswordRequest;
use OAT\Library\EnvironmentManagementClient\Model\OAuth2Client;
use OAT\Library\EnvironmentManagementClient\Model\OAuth2User;
use OAT\Library\EnvironmentManagementClient\Model\ValidationResult;
use OAT\Library\EnvironmentManagementClient\Repository\OAuth2ClientRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class OAuth2ClientRepository implements OAuth2ClientRepositoryInterface
{
    use GrpcCallTrait;

    private Oauth2ClientServiceClient $grpcClient;
    private ?LoggerInterface $logger;

    public function __construct(Oauth2ClientServiceClient $grpcClient, ?LoggerInterface $logger = null)
    {
        $this->grpcClient = $grpcClient;
        $this->logger = $logger ?? new NullLogger();
    }

    public function find(string $clientId): OAuth2Client
    {
        $grpcRequest = new GetClientRequest();
        $grpcRequest->setId($clientId);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching OAuth2 Client', [
            'clientId' => $clientId,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return OAuth2Client::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->GetClient($grpcRequest, [], ['timeout' => 10 * 1000000]),
                GetClientRequest::class
            )
        );
    }

    public function findUser(string $clientId, string $username): Oauth2User
    {
        $grpcRequest = new GetClientUserRequest();

        $grpcRequest
            ->setId($clientId)
            ->setUsername($username);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching OAuth2 Username of Client', [
            'clientId' => $clientId,
            'username' => $username,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return Oauth2User::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->GetClientUser($grpcRequest, [], ['timeout' => 10 * 1000000]),
                GetClientUserRequest::class,
            )
        );
    }

    public function validateClientSecret(string $clientId, string $clientSecret): ValidationResult
    {
        $grpcRequest = new ValidateClientSecretRequest();
        $oauth2ClientSecret = new Oauth2ClientSecret();

        $oauth2ClientSecret
            ->setId($clientId)
            ->setSecret($clientSecret);

        $grpcRequest
            ->setOauth2ClientSecret($oauth2ClientSecret);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Validating OAuth2 Secret of Client', [
            'clientId' => $clientId,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return ValidationResult::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->ValidateClientSecret($grpcRequest, [], ['timeout' => 10 * 1000000]),
                GetClientUserRequest::class,
            )
        );
    }

    public function validateUserPassword(string $clientId, string $username, string $password): ValidationResult
    {
        $grpcRequest = new ValidateUserPasswordRequest();
        $Oauth2UserPassword = new Oauth2UserPassword();

        $Oauth2UserPassword
            ->setId($clientId)
            ->setUsername($username)
            ->setPassword($password);

        $grpcRequest
            ->setOauth2UserPassword($Oauth2UserPassword);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching OAuth2 User password of Client', [
            'clientId' => $clientId,
            'username' => $username,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return ValidationResult::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->ValidateUserPassword($grpcRequest, [], ['timeout' => 10 * 1000000]),
                ValidateUserPasswordRequest::class,
            )
        );
    }
}
