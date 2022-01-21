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

use Oat\Envmgmt\Common\LtiRegistration as ProtoLtiRegistration;

final class LtiRegistration
{
    private string $id;
    private string $clientId;
    private string $platformId;
    private string $toolId;
    private array $deploymentIds;
    private ?string $platformJwksUrl;
    private ?string $toolJwksUrl;
    private ?LtiKeyChain $platformKeyChain;
    private ?LtiKeyChain $toolKeyChain;
    private ?LtiPlatform $ltiPlatform;
    private ?LtiTool $ltiTool;
    private ?string $tenantId;

    public function __construct(
        string $id,
        string $clientId,
        string $platformId,
        string $toolId,
        array $deploymentIds,
        ?string $platformJwksUrl,
        ?string $toolJwksUrl,
        ?LtiKeyChain $platformKeyChain,
        ?LtiKeyChain $toolKeyChain,
        ?LtiPlatform $ltiPlatform,
        ?LtiTool $ltiTool,
        ?string $tenantId
    ) {
        $this->id = $id;
        $this->clientId = $clientId;
        $this->platformId = $platformId;
        $this->toolId = $toolId;
        $this->deploymentIds = $deploymentIds;
        $this->platformJwksUrl = $platformJwksUrl;
        $this->toolJwksUrl = $toolJwksUrl;
        $this->platformKeyChain = $platformKeyChain;
        $this->toolKeyChain = $toolKeyChain;
        $this->ltiPlatform = $ltiPlatform;
        $this->ltiTool = $ltiTool;
        $this->tenantId = $tenantId;
    }

    public static function fromProtobuf(ProtoLtiRegistration $protoRegistration): self
    {
        return new self(
            $protoRegistration->getId(),
            $protoRegistration->getClientId(),
            $protoRegistration->getPlatformId(),
            $protoRegistration->getToolId(),
            iterator_to_array($protoRegistration->getDeploymentIds()),
            $protoRegistration->getPlatformJwksUrl(),
            $protoRegistration->getToolJwksUrl(),
            $protoRegistration->hasPlatformKeyChain()
                ? LtiKeyChain::fromProtobuf($protoRegistration->getPlatformKeyChain())
                : null,
            $protoRegistration->hasToolKeyChain()
                ? LtiKeyChain::fromProtobuf($protoRegistration->getToolKeyChain())
                : null,
            $protoRegistration->hasPlatform()
                ? LtiPlatform::fromProtobuf($protoRegistration->getPlatform())
                : null,
            $protoRegistration->hasTool()
                ? LtiTool::fromProtobuf($protoRegistration->getTool())
                : null,
            $protoRegistration->getTenantId()
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getPlatformId(): string
    {
        return $this->platformId;
    }

    public function getToolId(): string
    {
        return $this->toolId;
    }

    public function getDeploymentIds(): array
    {
        return $this->deploymentIds;
    }

    public function getPlatformJwksUrl(): ?string
    {
        return $this->platformJwksUrl;
    }

    public function getToolJwksUrl(): ?string
    {
        return $this->toolJwksUrl;
    }

    public function getPlatformKeyChain(): ?LtiKeyChain
    {
        return $this->platformKeyChain;
    }

    public function getToolKeyChain(): ?LtiKeyChain
    {
        return $this->toolKeyChain;
    }

    public function getLtiPlatform(): ?LtiPlatform
    {
        return $this->ltiPlatform;
    }

    public function getLtiTool(): ?LtiTool
    {
        return $this->ltiTool;
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }
}
