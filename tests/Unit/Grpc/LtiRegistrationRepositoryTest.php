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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Grpc;

use Oat\Envmgmt\Common\LtiKeyChain;
use Oat\Envmgmt\Common\LtiRegistration as ProtoLtiRegistration;
use Oat\Envmgmt\Common\LtiRegistrationCollection as ProtoLtiRegistrationCollection;
use Oat\Envmgmt\Sidecar\GetRegistrationRequest;
use Oat\Envmgmt\Sidecar\ListRegistrationsRequest;
use Oat\Envmgmt\Sidecar\LtiServiceClient;
use OAT\Library\EnvironmentManagementClient\Grpc\LtiRegistrationRepository;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistrationCollection;
use OAT\Library\EnvironmentManagementClient\Tests\Traits\GrpcTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LtiRegistrationRepositoryTest extends TestCase
{
    use GrpcTestingTrait;

    /** @var LtiServiceClient|MockObject */
    private $mockGrpcClient;
    private LtiRegistrationRepository $repository;

    protected function setUp(): void
    {
        $this->mockGrpcClient = $this->createMock(LtiServiceClient::class);
        $this->repository = new LtiRegistrationRepository($this->mockGrpcClient);
    }

    public function testFindSuccessfullyRuns(): void
    {
        $keyChain1 = new LtiKeyChain();
        $keyChain1->setId('key-chain-1');
        $keyChain1->setKeySetName('key-set-1');
        $keyChain1->setPublicKey('key-pub');
        $keyChain1->setPrivateKey('key-private');
        $keyChain1->setPrivateKeyPassphrase('pass1');

        $protoReg = new ProtoLtiRegistration();
        $protoReg->setId('reg-1');
        $protoReg->setClientId('client-1');
        $protoReg->setPlatformId('platform-1');
        $protoReg->setToolId('tool-1');
        $protoReg->setPlatformJwksUrl('platform-url');
        $protoReg->setToolJwksUrl('tool-url');
        $protoReg->setPlatformKeyChain($keyChain1);
        $protoReg->setToolKeyChain($keyChain1);
        $protoReg->setDeploymentIds(['deploy-1', 'deploy-2']);

        $this->mockGrpcClient->expects($this->once())
            ->method('GetRegistration')
            ->with($this->callback(function (GetRegistrationRequest $subject) {
                $this->assertSame('reg-1', $subject->getRegistrationId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoReg));

        $registration = $this->repository->find('reg-1');

        $this->assertInstanceOf(LtiRegistration::class, $registration);
        $this->assertEquals('reg-1', $registration->getId());
        $this->assertEquals('client-1', $registration->getClientId());
        $this->assertEquals('platform-1', $registration->getPlatformId());
        $this->assertEquals('tool-1', $registration->getToolId());
        $this->assertEquals('platform-url', $registration->getPlatformJwksUrl());
        $this->assertEquals('tool-url', $registration->getToolJwksUrl());
        $this->assertEquals(['deploy-1', 'deploy-2'], $registration->getDeploymentIds());
    }

    public function testFindAllSuccessfullyRuns(): void
    {
        $keyChain1 = new LtiKeyChain();
        $keyChain1->setId('key-chain-1');
        $keyChain1->setKeySetName('key-set-1');
        $keyChain1->setPublicKey('key-pub');
        $keyChain1->setPrivateKey('key-private');
        $keyChain1->setPrivateKeyPassphrase('pass1');

        $protoReg = new ProtoLtiRegistration();
        $protoReg->setId('reg-1');
        $protoReg->setClientId('client-1');
        $protoReg->setPlatformId('platform-1');
        $protoReg->setToolId('tool-1');
        $protoReg->setPlatformJwksUrl('platform-url');
        $protoReg->setToolJwksUrl('tool-url');
        $protoReg->setPlatformKeyChain($keyChain1);
        $protoReg->setToolKeyChain($keyChain1);
        $protoReg->setDeploymentIds(['deploy-1', 'deploy-2']);

        $protoCollection = new ProtoLtiRegistrationCollection();
        $protoCollection->setData([$protoReg]);

        $this->mockGrpcClient->expects($this->once())
            ->method('ListRegistrations')
            ->with($this->callback(function (ListRegistrationsRequest $subject) {
                $this->assertSame('client-id', $subject->getClientId());
                $this->assertSame('platform-iss', $subject->getPlatformIssuer());
                $this->assertSame('tool-iss', $subject->getToolIssuer());

                return true;
            }))
            ->willReturn($this->createMockCall($protoCollection));

        $collection = $this->repository->findAll('client-id', 'platform-iss', 'tool-iss');

        $this->assertInstanceOf(LtiRegistrationCollection::class, $collection);
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->has('reg-1'));
    }
}
