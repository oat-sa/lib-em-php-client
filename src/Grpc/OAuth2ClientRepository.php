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

use Oat\Envmgmt\Sidecar\GetClientRequest;
use Oat\Envmgmt\Sidecar\Oauth2ClientServiceClient;
use OAT\Library\EnvironmentManagementClient\Model\OAuth2Client;
use OAT\Library\EnvironmentManagementClient\Repository\OAuth2ClientRepositoryInterface;

final class OAuth2ClientRepository implements OAuth2ClientRepositoryInterface
{
    use GrpcCallTrait;

    private Oauth2ClientServiceClient $grpcClient;

    public function __construct(Oauth2ClientServiceClient $grpcClient)
    {
        $this->grpcClient = $grpcClient;
    }

    public function find(string $clientId): OAuth2Client
    {
        $grpcRequest = new GetClientRequest();
        $grpcRequest->setId($clientId);

        return OAuth2Client::fromProtobuf($this->doUnaryCall(
            $this->grpcClient->GetClient($grpcRequest),
            GetClientRequest::class
        ));
    }
}
