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

use Nyholm\Psr7\Factory\Psr17Factory;
use OAT\Library\EnvironmentManagementClient\Exception\TenantIdNotFoundException;
use OAT\Library\EnvironmentManagementClient\Http\TenantIdHeaderExtractor;
use PHPUnit\Framework\TestCase;

final class TenantIdHeaderExtractorTest extends TestCase
{
    public function testExtractTenantIdWithDefaultPrefix(): void
    {
        $message = (new Psr17Factory())->createResponse();
        $message = $message->withHeader('X-OAT-Tenant-Id', 'tenant-zzz')
            ->withAddedHeader('X-Oat-Tenant-Id', 'tenant-zzz-2')
            ->withHeader('Some-Other-Custom-Flag-2', 'some');

        $extractor = new TenantIdHeaderExtractor();
        $tenantId = $extractor->extract($message);

        $this->assertEquals('tenant-zzz-2', $tenantId);
    }

    public function testExtractTenantIdThrowsExceptionIfNoTenantHeaderPresent(): void
    {
        $message = (new Psr17Factory())->createResponse();
        $message = $message->withHeader('Some-Other-Custom-Flag-2', 'some');

        $this->expectException(TenantIdNotFoundException::class);
        $this->expectExceptionMessage('Tenant Id not found in request header.');

        $extractor = new TenantIdHeaderExtractor();
        $extractor->extract($message);
    }
}
