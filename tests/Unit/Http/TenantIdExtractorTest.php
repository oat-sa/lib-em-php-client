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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Http;

use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\UnencryptedToken;
use Nyholm\Psr7\Factory\Psr17Factory;
use OAT\Library\EnvironmentManagementClient\Exception\TenantIdNotFoundException;
use OAT\Library\EnvironmentManagementClient\Http\JWTTokenExtractorInterface;
use OAT\Library\EnvironmentManagementClient\Http\TenantIdExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class TenantIdExtractorTest extends TestCase
{
    private TenantIdExtractor $subject;

    /** @var JWTTokenExtractorInterface|MockObject */
    private $jwtExtractorMock;

    protected function setUp(): void
    {
        $this->jwtExtractorMock = $this->createMock(JWTTokenExtractorInterface::class);
        $this->subject = new TenantIdExtractor($this->jwtExtractorMock);
    }

    public function testExtractTenantIdSuccessfully(): void
    {
        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');

        $tokenMock = $this->createMock(UnencryptedToken::class);
        $claims = new DataSet(['tenant_id' => 'tenant-2'], '');

        $tokenMock->expects($this->exactly(2))
            ->method('claims')
            ->willReturn($claims);

        $this->jwtExtractorMock->expects($this->once())
            ->method('extract')
            ->with($request)
            ->willReturn($tokenMock);

        $tenantId = $this->subject->extract($request);

        $this->assertEquals('tenant-2', $tenantId);
    }

    public function testExtractTenantIdThrowsExceptionIfItNotPresentInToken(): void
    {
        $this->expectException(TenantIdNotFoundException::class);
        $this->expectExceptionMessage('Tenant Id not found in JWT token.');

        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');

        $tokenMock = $this->createMock(UnencryptedToken::class);
        $claims = new DataSet([], '');

        $tokenMock->expects($this->once())
            ->method('claims')
            ->willReturn($claims);

        $this->jwtExtractorMock->expects($this->once())
            ->method('extract')
            ->with($request)
            ->willReturn($tokenMock);

        $this->subject->extract($request);
    }
}
