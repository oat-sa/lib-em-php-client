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

use Oat\Envmgmt\Common\Configuration as ProtoConfiguration;
use Oat\Envmgmt\Common\ConfigurationCollection as ProtoConfigurationCollection;
use Oat\Envmgmt\Sidecar\ConfigServiceClient;
use Oat\Envmgmt\Sidecar\GetConfigRequest;
use Oat\Envmgmt\Sidecar\ListConfigsRequest;
use OAT\Library\EnvironmentManagementClient\Grpc\ConfigurationRepository;
use OAT\Library\EnvironmentManagementClient\Model\Configuration;
use OAT\Library\EnvironmentManagementClient\Model\ConfigurationCollection;
use OAT\Library\EnvironmentManagementClient\Tests\Traits\GrpcTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ConfigurationRepositoryTest extends TestCase
{
    use GrpcTestingTrait;

    /** @var ConfigServiceClient|MockObject */
    private $mockGrpcClient;
    private ConfigurationRepository $repository;

    protected function setUp(): void
    {
        $this->mockGrpcClient = $this->createMock(ConfigServiceClient::class);
        $this->repository = new ConfigurationRepository($this->mockGrpcClient);
    }

    public function testFindSuccessfullyRuns(): void
    {
        $protoConfiguration = new ProtoConfiguration();
        $protoConfiguration->setName('config-1');
        $protoConfiguration->setValue('value-1');

        $this->mockGrpcClient->expects($this->once())
            ->method('GetConfig')
            ->with($this->callback(function (GetConfigRequest $subject) {
                $this->assertSame('t1', $subject->getTenantId());
                $this->assertSame('conf-1', $subject->getConfigurationId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoConfiguration));

        $configuration = $this->repository->find('t1', 'conf-1');

        $this->assertInstanceOf(Configuration::class, $configuration);
        $this->assertEquals('config-1', $configuration->getName());
        $this->assertEquals('value-1', $configuration->getValue());
    }

    public function testFindAllSuccessfullyRuns(): void
    {
        $protoConfiguration = new ProtoConfiguration();
        $protoConfiguration->setName('config-1');
        $protoConfiguration->setValue('value-1');

        $protoCollection = new ProtoConfigurationCollection();
        $protoCollection->setData([$protoConfiguration]);

        $this->mockGrpcClient->expects($this->once())
            ->method('ListConfigs')
            ->with($this->callback(function (ListConfigsRequest $subject) {
                $this->assertSame('t1', $subject->getTenantId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoCollection));

        $collection = $this->repository->findAll('t1');

        $this->assertInstanceOf(ConfigurationCollection::class, $collection);
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->has('config-1'));
    }
}
