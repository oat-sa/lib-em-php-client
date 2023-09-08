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

use OAT\Library\EnvironmentManagementClient\Converter\LtiToolConverter;
use OAT\Library\EnvironmentManagementClient\Model\LtiTool;
use OAT\Library\Lti1p3Core\Tool\ToolInterface;
use PHPUnit\Framework\TestCase;

class LtiToolConverterTest extends TestCase
{
    private LtiToolConverter $subject;

    protected function setUp(): void
    {
        $this->subject = new LtiToolConverter();
    }

    public function testConvert(): void
    {
        $tool = new LtiTool(
            'tool-id',
            'tool-name',
            'tool-audience',
            'tool-oidc-initiation-url',
            'tool-launch-url',
            'tool-deep-linking-url',
        );

        $convertedLtiTool = $this->subject->convert($tool);

        $this->assertInstanceOf(ToolInterface::class, $convertedLtiTool);
        $this->assertSame('tool-id', $convertedLtiTool->getIdentifier());
        $this->assertSame('tool-name', $convertedLtiTool->getName());
        $this->assertSame('tool-audience', $convertedLtiTool->getAudience());
        $this->assertSame('tool-oidc-initiation-url', $convertedLtiTool->getOidcInitiationUrl());
        $this->assertSame('tool-launch-url', $convertedLtiTool->getLaunchUrl());
        $this->assertSame('tool-deep-linking-url', $convertedLtiTool->getDeepLinkingUrl());
    }
}
