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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Model;

use OAT\Library\EnvironmentManagementClient\Model\TenantAwareRegistration;
use OAT\Library\EnvironmentManagementClient\Model\TenantAwareRegistrationInterface;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use OAT\Library\Lti1p3Core\Registration\Registration;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainInterface;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TenantAwareRegistrationTest extends TestCase
{
    private PlatformInterface|MockObject $platformMock;
    private ToolInterface|MockObject $toolMock;
    private KeyChainInterface|MockObject $platformKeyChainMock;
    private KeyChainInterface|MockObject $toolKeyChainMock;

    protected function setUp(): void
    {
        $this->platformMock = $this->createMock(PlatformInterface::class);
        $this->toolMock = $this->createMock(ToolInterface::class);
        $this->platformKeyChainMock = $this->createMock(KeyChainInterface::class);
        $this->toolKeyChainMock = $this->createMock(KeyChainInterface::class);
    }

    public function testGetters(): void
    {
        $registration = new TenantAwareRegistration(
            'id',
            'clientId',
            $this->platformMock,
            $this->toolMock,
            ['1'],
            $this->platformKeyChainMock,
            $this->toolKeyChainMock,
            'platformJwksUrl',
            'toolJwksUrl',
            'tenantId'
        );

        $this->assertRegistrationGetters($registration);
    }

    public function testFromBaseRegistration(): void
    {
        $baseRegistration = new Registration(
            'id',
            'clientId',
            $this->platformMock,
            $this->toolMock,
            ['1'],
            $this->platformKeyChainMock,
            $this->toolKeyChainMock,
            'platformJwksUrl',
            'toolJwksUrl'
        );

        $registration = TenantAwareRegistration::fromBaseRegistration($baseRegistration, 'tenantId');

        $this->assertRegistrationGetters($registration);
    }

    private function assertRegistrationGetters(TenantAwareRegistrationInterface $registration): void
    {
        $this->assertSame('id', $registration->getIdentifier());
        $this->assertSame('clientId', $registration->getClientId());
        $this->assertSame($this->platformMock, $registration->getPlatform());
        $this->assertSame($this->toolMock, $registration->getTool());
        $this->assertSame(['1'], $registration->getDeploymentIds());
        $this->assertSame($this->platformKeyChainMock, $registration->getPlatformKeyChain());
        $this->assertSame($this->toolKeyChainMock, $registration->getToolKeyChain());
        $this->assertSame('platformJwksUrl', $registration->getPlatformJwksUrl());
        $this->assertSame('toolJwksUrl', $registration->getToolJwksUrl());
        $this->assertSame('tenantId', $registration->getTenantIdentifier());
    }
}
