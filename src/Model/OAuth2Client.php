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

namespace OAT\Library\EnvironmentManagementClient\Model;

use Oat\Envmgmt\Common\Oauth2Client as ProtoOauth2Client;

class OAuth2Client
{
    private string $name;
    private string $clientId;
    private string $clientSecret;
    private bool $isConfidential;
    private array $scopes;
    private ?string $tenantId;
    private ?string $instanceUrl;

    public function __construct(
        string $name,
        string $clientId,
        string $clientSecret,
        bool $isConfidential,
        array $scopes,
        ?string $tenantId,
        ?string $instanceUrl
    ) {
        $this->name = $name;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->isConfidential = $isConfidential;
        $this->scopes = $scopes;
        $this->tenantId = $tenantId;
        $this->instanceUrl = $instanceUrl;
    }

    public static function fromProtobuf(ProtoOauth2Client $protoOauth2Client): self
    {
        return new self(
            $protoOauth2Client->getName(),
            $protoOauth2Client->getClientId(),
            $protoOauth2Client->getClientSecret(),
            $protoOauth2Client->getIsConfidential(),
            iterator_to_array($protoOauth2Client->getScopes()),
            $protoOauth2Client->hasTenantId() ? $protoOauth2Client->getTenantId() : null,
            $protoOauth2Client->hasInstanceUrl() ? $protoOauth2Client->getInstanceUrl() : null
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getIsConfidential(): bool
    {
        return $this->isConfidential;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function getInstanceUrl(): ?string
    {
        return $this->instanceUrl;
    }
}
