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
use OAT\Library\EnvironmentManagementClient\Http\AuthorizationDetailsHeaderMarker;
use PHPUnit\Framework\TestCase;

final class AuthorizationDetailsHeaderMarkerTest extends TestCase
{
    public function testAuthDetailsHeaderAdded(): void
    {
        $message = (new Psr17Factory())->createResponse();

        $marker = new AuthorizationDetailsHeaderMarker();
        $message = $marker->withAuthDetails($message, "client1", "refreshToken1", "userIdentifier1", "userRole1", "cookieDomain1", "ltiToken1", "cookie");

        $this->assertTrue($message->hasHeader('X-OAT-WITH-AUTH-DETAILS'));

        $withAuthDetails = $message->getHeader('X-OAT-WITH-AUTH-DETAILS')[0];

        $this->assertNotNull(
            $withAuthDetails,
            "withAuthDetails is null"
        );

        $res_array = (array)json_decode($withAuthDetails);

        $this->assertArrayHasKey('clientId', $res_array);
        $this->assertEquals('client1', $res_array['clientId']);
        $this->assertArrayHasKey('refreshTokenId', $res_array);
        $this->assertEquals('refreshToken1', $res_array['refreshTokenId']);
        $this->assertEquals('userIdentifier1', $res_array['userIdentifier']);
        $this->assertEquals('userRole1', $res_array['userRole']);
        $this->assertEquals('cookieDomain1', $res_array['cookieDomain']);
        $this->assertEquals('ltiToken1', $res_array['ltiToken']);
        $this->assertEquals('cookie', $res_array['mode']);
    }
}
