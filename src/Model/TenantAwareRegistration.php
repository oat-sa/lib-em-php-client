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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace OAT\Library\EnvironmentManagementClient\Model;

use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Registration\Registration as BaseRegistration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;

class TenantAwareRegistration extends BaseRegistration implements TenantAwareRegistrationInterface
{
    public function __construct(
        string $identifier,
        string $clientId,
        PlatformInterface $platform,
        ToolInterface $tool,
        array $deploymentIds,
        ?KeyChainInterface $platformKeyChain = null,
        ?KeyChainInterface $toolKeyChain = null,
        ?string $platformJwksUrl = null,
        ?string $toolJwksUrl = null,
        private ?string $tenantId = null
    ) {
        parent::__construct(
            $identifier,
            $clientId,
            $platform,
            $tool,
            $deploymentIds,
            $platformKeyChain,
            $toolKeyChain,
            $platformJwksUrl,
            $toolJwksUrl
        );
    }
    public function getTenantIdentifier(): ?string
    {
        return $this->tenantId;
    }

    public static function fromBaseRegistration(RegistrationInterface $registration, ?string $tenantId = null): TenantAwareRegistrationInterface
    {
        return new TenantAwareRegistration(
            $registration->getIdentifier(),
            $registration->getClientId(),
            $registration->getPlatform(),
            $registration->getTool(),
            $registration->getDeploymentIds(),
            $registration->getPlatformKeyChain(),
            $registration->getToolKeyChain(),
            $registration->getPlatformJwksUrl(),
            $registration->getToolJwksUrl(),
            $tenantId
        );
    }
}
