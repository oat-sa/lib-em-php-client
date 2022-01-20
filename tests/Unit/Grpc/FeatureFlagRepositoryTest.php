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

use Oat\Envmgmt\Common\FeatureFlag as ProtoFeatureFlag;
use Oat\Envmgmt\Common\FeatureFlagCollection as ProtoFeatureFlagCollection;
use Oat\Envmgmt\Sidecar\FeatureFlagServiceClient;
use Oat\Envmgmt\Sidecar\GetFeatureFlagRequest;
use Oat\Envmgmt\Sidecar\ListFeatureFlagsRequest;
use OAT\Library\EnvironmentManagementClient\Grpc\FeatureFlagRepository;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlagCollection;
use OAT\Library\EnvironmentManagementClient\Tests\Traits\GrpcTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class FeatureFlagRepositoryTest extends TestCase
{
    use GrpcTestingTrait;

    /** @var FeatureFlagServiceClient|MockObject */
    private $mockGrpcClient;
    private FeatureFlagRepository $repository;

    protected function setUp(): void
    {
        $this->mockGrpcClient = $this->createMock(FeatureFlagServiceClient::class);
        $this->repository = new FeatureFlagRepository($this->mockGrpcClient);
    }

    public function testFindSuccessfullyRuns(): void
    {
        $protoFlag = new ProtoFeatureFlag();
        $protoFlag->setName('flag-1');
        $protoFlag->setValue('value-1');

        $this->mockGrpcClient->expects($this->once())
            ->method('GetFeatureFlag')
            ->with($this->callback(function (GetFeatureFlagRequest $subject) {
                $this->assertSame('t1', $subject->getTenantId());
                $this->assertSame('flag-1', $subject->getFeatureFlagId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoFlag));

        $flag = $this->repository->find('t1', 'flag-1');

        $this->assertInstanceOf(FeatureFlag::class, $flag);
        $this->assertEquals('flag-1', $flag->getName());
        $this->assertEquals('value-1', $flag->getValue());
    }

    public function testFindAllSuccessfullyRuns(): void
    {
        $protoConfiguration = new ProtoFeatureFlag();
        $protoConfiguration->setName('flag-1');
        $protoConfiguration->setValue('value-1');

        $protoCollection = new ProtoFeatureFlagCollection();
        $protoCollection->setData([$protoConfiguration]);

        $this->mockGrpcClient->expects($this->once())
            ->method('ListFeatureFlags')
            ->with($this->callback(function (ListFeatureFlagsRequest $subject) {
                $this->assertSame('t1', $subject->getTenantId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoCollection));

        $collection = $this->repository->findAll('t1');

        $this->assertInstanceOf(FeatureFlagCollection::class, $collection);
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->has('flag-1'));
    }
}
