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

use OAT\Library\EnvironmentManagementClient\Model\LtiKeyChain;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistrationCollection;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;

final class LtiRegistrationCollectionTest extends TestCase
{
    public function testRegistrationCollectionLifeCycle(): void
    {
        $collection = new LtiRegistrationCollection();

        $this->assertEmpty($collection->all());
        $this->assertEmpty($collection->getIterator());
        $this->assertEquals(0, $collection->count());
        $this->assertTrue($collection->isEmpty());

        $reg1 = new LtiRegistration(
            'reg-1',
            'client-1',
            'platform-1',
            'tool-1',
            ['deploy-1', 'deploy-2'],
            'platform-url',
            'tool-url',
            new LtiKeyChain(
                'platform-key-1',
                'platform-keys',
                'public-key',
                'private-key',
                'private-pass'
            ),
            new LtiKeyChain(
                'tool-key-1',
                'tool-keys',
                'public-key',
                'private-key',
                'private-pass'
            ),
            null,
            null,
        );

        $collection->add($reg1);

        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->has('reg-1'));
        $this->assertEquals($reg1, $collection->get('reg-1'));
        $this->assertEquals([$reg1], $collection->all());
    }

    public function testRegistrationCollectionGetThrowsExceptionWhenFlagNotExist(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('LTI Registration FAKE-reg-1 does not exist.');

        $collection = new LtiRegistrationCollection();
        $collection->get('FAKE-reg-1');
    }
}
