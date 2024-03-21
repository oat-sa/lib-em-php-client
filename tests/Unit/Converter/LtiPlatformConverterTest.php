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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Converter;

use OAT\Library\EnvironmentManagementClient\Converter\LtiPlatformConverter;
use OAT\Library\EnvironmentManagementClient\Model\LtiPlatform;
use OAT\Library\Lti1p3Core\Platform\PlatformInterface;
use PHPUnit\Framework\TestCase;

class LtiPlatformConverterTest extends TestCase
{
    private LtiPlatformConverter $subject;

    protected function setUp(): void
    {
        $this->subject = new LtiPlatformConverter();
    }

    public function testConvert(): void
    {
        $platform = new LtiPlatform(
            'platform-id',
            'platform-name',
            'platform-audience',
            'platform-oidc-authentication-url',
            'platform-oauth2-access-token-url'
        );

        $convertedLtiPlatform = $this->subject->convert($platform);

        $this->assertInstanceOf(PlatformInterface::class, $convertedLtiPlatform);
        $this->assertSame('platform-id', $convertedLtiPlatform->getIdentifier());
        $this->assertSame('platform-name', $convertedLtiPlatform->getName());
        $this->assertSame('platform-audience', $convertedLtiPlatform->getAudience());
        $this->assertSame('platform-oidc-authentication-url', $convertedLtiPlatform->getOidcAuthenticationUrl());
        $this->assertSame('platform-oauth2-access-token-url', $convertedLtiPlatform->getOAuth2AccessTokenUrl());
    }
}
