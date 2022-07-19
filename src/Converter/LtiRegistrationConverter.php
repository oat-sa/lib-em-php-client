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

namespace OAT\Library\EnvironmentManagementClient\Converter;

use InvalidArgumentException;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\TenantAwareRegistration;
use OAT\Library\EnvironmentManagementClient\Model\TenantAwareRegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactoryInterface;

class LtiRegistrationConverter
{
    public function __construct(
        private LtiPlatformConverter $platformConverter,
        private LtiToolConverter $toolConverter,
        private KeyChainFactoryInterface $keyChainFactory,
    ) {}

    public function convert(LtiRegistration $ltiRegistration): RegistrationInterface
    {
        if (null === $ltiRegistration->getLtiPlatform()) {
            throw new InvalidArgumentException(sprintf(
                'LTI Platform not returned for Registration %s',
                $ltiRegistration->getId()
            ));
        }

        if (null === $ltiRegistration->getLtiTool()) {
            throw new InvalidArgumentException(sprintf(
                'LTI Tool not returned for Registration %s',
                $ltiRegistration->getId()
            ));
        }

        $ltiPlatformKeyChain = $ltiRegistration->getPlatformKeyChain();
        $ltiToolKeyChain = $ltiRegistration->getToolKeyChain();

        return new Registration(
            $ltiRegistration->getId(),
            $ltiRegistration->getClientId(),
            $this->platformConverter->convert($ltiRegistration->getLtiPlatform()),
            $this->toolConverter->convert($ltiRegistration->getLtiTool()),
            $ltiRegistration->getDeploymentIds(),
            $ltiPlatformKeyChain
            && $ltiPlatformKeyChain->getPublicKey()
            && $ltiPlatformKeyChain->getPrivateKey()
            && $ltiPlatformKeyChain->getKeySetName()
                ? $this->keyChainFactory->create(
                $ltiPlatformKeyChain?->getId(),
                $ltiPlatformKeyChain?->getKeySetName(),
                $ltiPlatformKeyChain?->getPublicKey(),
                $ltiPlatformKeyChain?->getPrivateKey(),
                $ltiPlatformKeyChain?->getPrivateKeyPassphrase(),
            )
                : null,
            $ltiToolKeyChain
            && $ltiToolKeyChain->getPublicKey()
            && $ltiToolKeyChain->getPrivateKey()
            && $ltiToolKeyChain->getKeySetName()
                ? $this->keyChainFactory->create(
                $ltiToolKeyChain->getId(),
                $ltiToolKeyChain->getKeySetName(),
                $ltiToolKeyChain->getPublicKey(),
                $ltiToolKeyChain->getPrivateKey(),
                $ltiToolKeyChain->getPrivateKeyPassphrase(),
            )
                : null,
            $ltiRegistration->getPlatformJwksUrl(),
            $ltiRegistration->getToolJwksUrl()
        );
    }

    public function convertWithTenantId(LtiRegistration $ltiRegistration): TenantAwareRegistrationInterface
    {
        return TenantAwareRegistration::fromBaseRegistration(
            $this->convert($ltiRegistration),
            $ltiRegistration->getTenantId()
        );
    }
}
