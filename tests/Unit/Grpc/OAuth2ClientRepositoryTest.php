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

use Oat\Envmgmt\Common\Oauth2Client as ProtoOauth2Client;
use Oat\Envmgmt\Sidecar\GetClientRequest;
use Oat\Envmgmt\Sidecar\Oauth2ClientServiceClient;
use OAT\Library\EnvironmentManagementClient\Grpc\OAuth2ClientRepository;
use OAT\Library\EnvironmentManagementClient\Model\OAuth2Client;
use OAT\Library\EnvironmentManagementClient\Tests\Traits\GrpcTestingTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class OAuth2ClientRepositoryTest extends TestCase
{
    use GrpcTestingTrait;

    /** @var Oauth2ClientServiceClient|MockObject */
    private $mockGrpcClient;
    private OAuth2ClientRepository $repository;

    protected function setUp(): void
    {
        $this->mockGrpcClient = $this->createMock(Oauth2ClientServiceClient::class);
        $this->repository = new OAuth2ClientRepository($this->mockGrpcClient);
    }

    public function testFindSuccessfullyRuns(): void
    {
        $protoClient = new ProtoOauth2Client();
        $protoClient->setName('Client-1');
        $protoClient->setClientId('client-1');
        $protoClient->setClientSecret('secret-1');
        $protoClient->setInstanceUrl('client-1-url');
        $protoClient->setScopes(['scope-1', 'scope-2']);
        $protoClient->setTenantId('t-1');

        $this->mockGrpcClient->expects($this->once())
            ->method('GetClient')
            ->with($this->callback(function (GetClientRequest $subject) {
                $this->assertSame('client-1', $subject->getId());

                return true;
            }))
            ->willReturn($this->createMockCall($protoClient));

        $client = $this->repository->find('client-1');

        $this->assertInstanceOf(OAuth2Client::class, $client);
        $this->assertEquals('Client-1', $client->getName());
        $this->assertEquals('client-1', $client->getClientId());
        $this->assertEquals('secret-1', $client->getClientSecret());
        $this->assertEquals(['scope-1', 'scope-2'], $client->getScopes());
        $this->assertEquals('client-1-url', $client->getInstanceUrl());
        $this->assertEquals('t-1', $client->getTenantId());
    }
}
