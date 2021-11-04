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

use Oat\Envmgmt\Sidecar\ConfigServiceClient;
use Oat\Envmgmt\Sidecar\GetConfigRequest;
use Oat\Envmgmt\Sidecar\ListConfigsRequest;
use OAT\Library\EnvironmentManagementClient\Model\Configuration;
use OAT\Library\EnvironmentManagementClient\Model\ConfigurationCollection;
use OAT\Library\EnvironmentManagementClient\Repository\ConfigurationRepositoryInterface;

final class ConfigurationRepository implements ConfigurationRepositoryInterface
{
    use GrpcCallTrait;

    private ConfigServiceClient $grpcClient;

    public function __construct(ConfigServiceClient $grpcClient)
    {
        $this->grpcClient = $grpcClient;
    }

    public function find(string $tenantId, string $configId): Configuration
    {
        $grpcRequest = new GetConfigRequest();
        $grpcRequest->setTenantId($tenantId);
        $grpcRequest->setConfigurationId($configId);

        return Configuration::fromProtobuf($this->doUnaryCall(
            $this->grpcClient->GetConfig($grpcRequest),
            GetConfigRequest::class
        ));
    }

    public function findAll(string $tenantId): ConfigurationCollection
    {
        $grpcRequest = new ListConfigsRequest();
        $grpcRequest->setTenantId($tenantId);

        return ConfigurationCollection::fromProtobuf($this->doUnaryCall(
            $this->grpcClient->ListConfigs($grpcRequest),
            ListConfigsRequest::class
        ));
    }
}
