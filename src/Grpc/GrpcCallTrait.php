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

namespace OAT\Library\EnvironmentManagementClient\Grpc;

use Exception;
use Grpc\BaseStub;
use Grpc\UnaryCall;
use OAT\Library\EnvironmentManagementClient\Exception\GrpcCallFailedException;
use Throwable;

use const Grpc\STATUS_OK;

trait GrpcCallTrait
{
    private function doUnaryCall(UnaryCall $call, string $requestName)
    {
        try {
            [$grpcResponse, $grpcStatus] = $call->wait();
        } catch (Throwable $throwable) {
            throw GrpcCallFailedException::duringCall($requestName, $throwable);
        }

        if (STATUS_OK !== $grpcStatus->code) {
            throw GrpcCallFailedException::afterCallWithErrorStatus($grpcStatus);
        }

        return $grpcResponse;
    }

    /**
     * @throws Exception
     */
    private function checkClientAvailability(BaseStub $client): void
    {
        if (!$client->waitForReady(10 * 1000000)) { // 10 seconds
            throw GrpcCallFailedException::serverNotReady();
        }
    }
}
