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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Model;

use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlagCollection;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class FeatureFlagCollectionTest extends TestCase
{
    public function testFlagCollectionLifeCycle(): void
    {
        $collection = new FeatureFlagCollection();

        $this->assertEmpty($collection->all());
        $this->assertEmpty($collection->getIterator());
        $this->assertEquals(0, $collection->count());
        $this->assertTrue($collection->isEmpty());

        $collection->add('flAG-1', 'value-1');

        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->has('flag-1'));
        $this->assertEquals(new FeatureFlag('flAG-1', 'value-1'), $collection->get('flAG-1'));
        $this->assertEquals([new FeatureFlag('flAG-1', 'value-1')], $collection->all());
    }

    public function testFlagCollectionGetThrowsExceptionWhenFlagNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Flag FAKE-flag-1 does not exist.');

        $collection = new FeatureFlagCollection();
        $collection->get('FAKE-flag-1');
    }
}
