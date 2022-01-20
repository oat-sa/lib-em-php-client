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

namespace OAT\Library\EnvironmentManagementClient\Exception;

use Throwable;
use const Grpc\STATUS_UNKNOWN;

final class GrpcCallFailedException extends EnvironmentManagementClientException
{
    public static function duringCall(string $requestName, Throwable $previous): self
    {
        return new self(
            sprintf('gRPC call for %s failed.', $requestName),
            STATUS_UNKNOWN,
            $previous
        );
    }

    public static function afterCallWithErrorStatus(object $grpcStatus): self
    {
        return new self(
            sprintf('gRPC call returned with error: %s', $grpcStatus->details),
            $grpcStatus->code
        );
    }
}
