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

use Oat\Envmgmt\Sidecar\GetRegistrationRequest;
use Oat\Envmgmt\Sidecar\ListRegistrationsRequest;
use Oat\Envmgmt\Sidecar\LtiServiceClient;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistrationCollection;
use OAT\Library\EnvironmentManagementClient\Repository\LtiRegistrationRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LtiRegistrationRepository implements LtiRegistrationRepositoryInterface
{
    use GrpcCallTrait;

    private LtiServiceClient $grpcClient;
    private ?LoggerInterface $logger;

    public function __construct(LtiServiceClient $grpcClient, ?LoggerInterface $logger = null)
    {
        $this->grpcClient = $grpcClient;
        $this->logger = $logger ?? new NullLogger();
    }

    public function find(string $registrationId): LtiRegistration
    {
        $grpcRequest = new GetRegistrationRequest();
        $grpcRequest->setRegistrationId($registrationId);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching Lti Registration', [
            'registrationId' => $registrationId,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return LtiRegistration::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->GetRegistration($grpcRequest),
                GetRegistrationRequest::class
            )
        );
    }

    public function findAll(
        ?string $clientId = null,
        ?string $platformIssuer = null,
        ?string $toolIssuer = null
    ): LtiRegistrationCollection {
        $grpcRequest = new ListRegistrationsRequest();

        if (null !== $clientId) {
            $grpcRequest->setClientId($clientId);
        }

        if (null !== $platformIssuer) {
            $grpcRequest->setPlatformIssuer($platformIssuer);
        }

        if (null !== $toolIssuer) {
            $grpcRequest->setToolIssuer($toolIssuer);
        }

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching all Lti Registrations', [
            'clientId' => $clientId,
            'platformIssuer' => $platformIssuer,
            'toolIssuer' => $toolIssuer,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return LtiRegistrationCollection::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->ListRegistrations($grpcRequest, [], ['timeout' => 10 * 1000000]),
                ListRegistrationsRequest::class
            )
        );
    }
}
