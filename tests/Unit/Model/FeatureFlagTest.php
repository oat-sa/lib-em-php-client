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

use InvalidArgumentException;
use OAT\Library\EnvironmentManagementClient\Model\FeatureFlag;
use PHPUnit\Framework\TestCase;

final class FeatureFlagTest extends TestCase
{
    /**
     * @dataProvider invalidDataProvider
     */
    public function testFeatureFlagWithInvalidData(string $name, string $value, string $expectedExceptionMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        new FeatureFlag($name, $value);
    }

    public function invalidDataProvider(): array
    {
        return [
            'empty_name' => ['', 'val', 'Flag name cannot be empty.'],
            'empty_value' => ['name', '', 'Flag value cannot be empty.'],
        ];
    }

    public function testGetters(): void
    {
        $flag = new FeatureFlag('flag-1', 'value-1');
        $this->assertEquals('flag-1', $flag->getName());
        $this->assertEquals('value-1', $flag->getValue());
    }
}
