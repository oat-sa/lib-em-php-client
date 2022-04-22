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

use Oat\Envmgmt\Sidecar\FeatureFlagServiceClient;
use Oat\Envmgmt\Sidecar\GetFeatureFlagRequest;
use Oat\Envmgmt\Sidecar\ListFeatureFlagsRequest;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlagCollection;
use OAT\Library\EnvironmentManagementClient\Repository\FeatureFlagRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class FeatureFlagRepository implements FeatureFlagRepositoryInterface
{
    use GrpcCallTrait;

    private FeatureFlagServiceClient $grpcClient;
    private ?LoggerInterface $logger;

    public function __construct(FeatureFlagServiceClient $grpcClient, ?LoggerInterface $logger = null)
    {
        $this->grpcClient = $grpcClient;
        $this->logger = $logger ?? new NullLogger();
    }

    public function find(string $tenantId, string $featureFlagId): FeatureFlag
    {
        $grpcRequest = new GetFeatureFlagRequest();
        $grpcRequest->setTenantId($tenantId);
        $grpcRequest->setFeatureFlagId($featureFlagId);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching Feature Flag', [
            'tenantId' => $tenantId,
            'featureFlagId' => $featureFlagId,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return FeatureFlag::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->GetFeatureFlag($grpcRequest, [], ['timeout' => 10 * 1000000]),
                GetFeatureFlagRequest::class
            )
        );
    }

    public function findAll(string $tenantId): FeatureFlagCollection
    {
        $grpcRequest = new ListFeatureFlagsRequest();
        $grpcRequest->setTenantId($tenantId);

        $this->checkClientAvailability($this->grpcClient);

        $this->logger->debug('Fetching all Feature Flags', [
            'tenantId' => $tenantId,
            'grpc_endpoint' => $this->grpcClient->getTarget(),
        ]);

        return FeatureFlagCollection::fromProtobuf(
            $this->doUnaryCall(
                $this->grpcClient->ListFeatureFlags($grpcRequest, [], ['timeout' => 10 * 1000000]),
                ListFeatureFlagsRequest::class
            )
        );
    }
}
