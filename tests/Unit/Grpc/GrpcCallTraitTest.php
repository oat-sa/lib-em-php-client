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

use Grpc\UnaryCall;
use OAT\Library\EnvironmentManagementClient\Exception\GrpcCallFailedException;
use OAT\Library\EnvironmentManagementClient\Grpc\GrpcCallTrait;
use OAT\Library\EnvironmentManagementClient\Tests\Traits\GrpcTestingTrait;
use PHPUnit\Framework\TestCase;

final class GrpcCallTraitTest extends TestCase
{
    use GrpcTestingTrait;

    public function testItThrowsExceptionWhenUnaryCallFails(): void
    {
        $this->expectException(GrpcCallFailedException::class);
        $this->expectExceptionMessage('gRPC call for RequestPrimary failed.');

        $this->createClassWithTrait()
            ->proxy($this->createMockCallWithException(), 'RequestPrimary');
    }

    public function testItThrowsExceptionWhenErrorStatusReturned(): void
    {
        $this->expectException(GrpcCallFailedException::class);
        $this->expectExceptionMessage('gRPC call returned with error: Some message');
        $this->expectExceptionCode(21);

        $this->createClassWithTrait()
            ->proxy($this->createMockCall(null, 21, 'Some message.'), 'RequestPrimary');
    }

    private function createClassWithTrait(): object
    {
        return new class() {
            use GrpcCallTrait;

            public function proxy(UnaryCall $call, string $requestName)
            {
                return $this->doUnaryCall($call, $requestName);
            }
        };
    }
}
