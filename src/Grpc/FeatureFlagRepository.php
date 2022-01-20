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

final class FeatureFlagRepository implements FeatureFlagRepositoryInterface
{
    use GrpcCallTrait;

    private FeatureFlagServiceClient $grpcClient;

    public function __construct(FeatureFlagServiceClient $grpcClient)
    {
        $this->grpcClient = $grpcClient;
    }

    public function find(string $tenantId, string $featureFlagId): FeatureFlag
    {
        $grpcRequest = new GetFeatureFlagRequest();
        $grpcRequest->setTenantId($tenantId);
        $grpcRequest->setFeatureFlagId($featureFlagId);

        return FeatureFlag::fromProtobuf($this->doUnaryCall(
            $this->grpcClient->GetFeatureFlag($grpcRequest),
            GetFeatureFlagRequest::class
        ));
    }

    public function findAll(string $tenantId): FeatureFlagCollection
    {
        $grpcRequest = new ListFeatureFlagsRequest();
        $grpcRequest->setTenantId($tenantId);

        return FeatureFlagCollection::fromProtobuf($this->doUnaryCall(
            $this->grpcClient->ListFeatureFlags($grpcRequest),
            ListFeatureFlagsRequest::class
        ));
    }
}
