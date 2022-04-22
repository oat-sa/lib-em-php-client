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
use Lcobucci\JWT\UnencryptedToken;
use Nyholm\Psr7\Factory\Psr17Factory;
use OAT\Library\EnvironmentManagementClient\Exception\RegistrationIdNotFoundException;
use OAT\Library\EnvironmentManagementClient\Exception\TenantIdNotFoundException;
use OAT\Library\EnvironmentManagementClient\Http\JWTTokenExtractorInterface;
use OAT\Library\EnvironmentManagementClient\Http\RegistrationIdExtractor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegistrationIdExtractorTest extends TestCase
{
    private RegistrationIdExtractor $subject;

    /** @var JWTTokenExtractorInterface|MockObject */
    private $jwtExtractorMock;

    protected function setUp(): void
    {
        $this->jwtExtractorMock = $this->createMock(JWTTokenExtractorInterface::class);
        $this->subject = new RegistrationIdExtractor($this->jwtExtractorMock);
    }

    public function testExtractRegistrationIdSuccessfully(): void
    {
        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');

        $tokenMock = $this->createMock(UnencryptedToken::class);
        $claims = new DataSet(['registration_id' => 'reg-2'], '');

        $tokenMock->expects($this->exactly(2))
            ->method('claims')
            ->willReturn($claims);

        $this->jwtExtractorMock->expects($this->once())
            ->method('extract')
            ->with($request)
            ->willReturn($tokenMock);

        $tenantId = $this->subject->extract($request);

        $this->assertEquals('reg-2', $tenantId);
    }

    public function testExtractRegistrationIdThrowsExceptionIfItNotPresentInToken(): void
    {
        $this->expectException(RegistrationIdNotFoundException::class);
        $this->expectExceptionMessage('LTI Registration Id not found in JWT token.');

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
