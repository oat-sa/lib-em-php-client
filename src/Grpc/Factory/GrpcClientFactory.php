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

namespace OAT\Library\EnvironmentManagementClient\Grpc\Factory;

use Grpc\ChannelCredentials;
use Oat\Envmgmt\Sidecar\ConfigServiceClient;
use Oat\Envmgmt\Sidecar\FeatureFlagServiceClient;
use Oat\Envmgmt\Sidecar\LtiServiceClient;
use Oat\Envmgmt\Sidecar\Oauth2ClientServiceClient;

final class GrpcClientFactory
{
    private string $hostname;
    private array $opts;

    public function __construct()
    {
        $this->hostname = sprintf(
            '%s:%s',
            getenv('EM_SIDECAR_HOST') ?: 'localhost',
            getenv('EM_SIDECAR_PORT') ?: '18084'
        );

        $this->opts = [
            'credentials' => ChannelCredentials::createInsecure(),
        ];
    }

    public function createConfigServiceClient(): ConfigServiceClient
    {
        return new ConfigServiceClient($this->hostname, $this->opts);
    }

    public function createFeatureFlagServiceClient(): FeatureFlagServiceClient
    {
        return new FeatureFlagServiceClient($this->hostname, $this->opts);
    }

    public function createLtiServiceClient(): LtiServiceClient
    {
        return new LtiServiceClient($this->hostname, $this->opts);
    }

    public function createOauth2ClientServiceClient(): Oauth2ClientServiceClient
    {
        return new Oauth2ClientServiceClient($this->hostname, $this->opts);
    }
}
