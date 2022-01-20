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
use OAT\Library\EnvironmentManagementClient\Http\FeatureFlagHeaderExtractor;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use PHPUnit\Framework\TestCase;

final class FeatureFlagHeaderExtractorTest extends TestCase
{
    public function testExtractFlagsWithDefaultPrefix(): void
    {
        $message = (new Psr17Factory())->createResponse();
        $message = $message->withHeader('X-OAT-Custom-Test-FLAG-1', 'xxx-YYY')
            ->withHeader('X-Oat-CuSTom-TeST-Flag-2', 'zzz')
            ->withAddedHeader('X-Oat-CuSTom-TeST-Flag-2', 'zzz-SECOND-VALUE')
            ->withAddedHeader('X-Oat-Custom-Test-Flag-2', 'zzz-THIRD-VALUE')
            ->withHeader('Some-Other-Custom-Flag-2', 'some');

        $extractor = new FeatureFlagHeaderExtractor();
        $flags = $extractor->extract($message);

        $this->assertCount(2, $flags);
        $this->assertEquals(
            [
                new FeatureFlag('Test.FLAG.1', 'xxx-YYY'),
                new FeatureFlag('TeST.Flag.2', 'zzz-THIRD-VALUE'),
            ],
            $flags->all()
        );
    }

    public function testExtractFlagsReturnsEmptyCollectionWhenNoFlagsPresent(): void
    {
        $message = (new Psr17Factory())->createResponse();
        $extractor = new FeatureFlagHeaderExtractor();
        $flags = $extractor->extract($message);

        $this->assertCount(0, $flags);
    }

    public function testExtractFlagsWithCustomHeaderPrefix(): void
    {
        $message = (new Psr17Factory())->createResponse();
        $message = $message->withHeader('CUSTOM-PREFIX-FLAG-1', 'xxx');

        $extractor = new FeatureFlagHeaderExtractor('CUSTOM-PREFIX-');
        $flags = $extractor->extract($message);

        $this->assertCount(1, $flags);
        $this->assertEquals(
            [
                new FeatureFlag('FLAG.1', 'xxx'),
            ],
            $flags->all()
        );
    }
}
