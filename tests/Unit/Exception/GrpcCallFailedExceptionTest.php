<?php

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Exception;

use Grpc\Status;
use InvalidArgumentException;
use OAT\Library\EnvironmentManagementClient\Exception\GrpcCallFailedException;
use PHPUnit\Framework\TestCase;
use const Grpc\STATUS_CANCELLED;
use const Grpc\STATUS_UNKNOWN;

class GrpcCallFailedExceptionTest extends TestCase
{
    public function testDuringCall(): void
    {
        $prevException = new InvalidArgumentException('message');
        $exception = GrpcCallFailedException::duringCall('requestName', $prevException);
        $this->assertEquals(new GrpcCallFailedException(
            'gRPC call for requestName failed.',
            STATUS_UNKNOWN,
            $prevException
        ), $exception);
    }

    public function testAfterCallWithErrorStatus(): void
    {
        $exception = GrpcCallFailedException::afterCallWithErrorStatus((object) Status::status(STATUS_CANCELLED, "details"));
        $this->assertEquals(new GrpcCallFailedException(
            'gRPC call returned with error: details',
            STATUS_CANCELLED
        ), $exception);
    }
}