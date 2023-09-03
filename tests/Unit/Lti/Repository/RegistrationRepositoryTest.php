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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Lti\Repository;

use OAT\Library\EnvironmentManagementClient\Converter\LtiRegistrationConverter;
use OAT\Library\EnvironmentManagementClient\Lti\Repository\RegistrationRepository;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistrationCollection;
use OAT\Library\EnvironmentManagementClient\Repository\LtiRegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RegistrationRepositoryTest extends TestCase
{
    /** @var RegistrationRepository */
    private RegistrationRepository $registrationRepository;

    /** @var LtiRegistrationRepositoryInterface|MockObject */
    private LtiRegistrationRepositoryInterface $ltiRegistrationRepositoryMock;

    /** @var LtiRegistrationConverter|MockObject */
    private LtiRegistrationConverter $ltiRegistrationConverterMock;

    protected function setUp(): void
    {
        $this->ltiRegistrationRepositoryMock = $this->createMock(LtiRegistrationRepositoryInterface::class);
        $this->ltiRegistrationConverterMock = $this->createMock(LtiRegistrationConverter::class);

        $this->registrationRepository = new RegistrationRepository(
            $this->ltiRegistrationRepositoryMock,
            $this->ltiRegistrationConverterMock
        );
    }

    public function testFind(): void
    {
        $ltiRegistrationMock = $this->createMock(LtiRegistration::class);
        $registrationMock = $this->createMock(RegistrationInterface::class);

        $this->ltiRegistrationRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with('id')
            ->willReturn($ltiRegistrationMock);

        $this->ltiRegistrationConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($ltiRegistrationMock)
            ->willReturn($registrationMock);

        $this->assertSame(
            $registrationMock,
            $this->registrationRepository->find('id')
        );
    }

    public function testFindAll(): void
    {
        $ltiRegistrationMock = $this->createMock(LtiRegistration::class);
        $ltiRegistrationCollectionMock = $this->createMock(LtiRegistrationCollection::class);
        $registrationMock = $this->createMock(RegistrationInterface::class);

        $this->ltiRegistrationRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($ltiRegistrationCollectionMock);

        $ltiRegistrationCollectionMock
            ->expects($this->once())
            ->method('all')
            ->willReturn([$ltiRegistrationMock]);

        $this->ltiRegistrationConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($ltiRegistrationMock)
            ->willReturn($registrationMock);

        $this->assertSame(
            [$registrationMock],
            $this->registrationRepository->findAll()
        );
    }

    public function testFindByClientId(): void
    {
        $ltiRegistrationMock = $this->createMock(LtiRegistration::class);
        $ltiRegistrationCollection = new LtiRegistrationCollection();
        $registrationMock = $this->createMock(RegistrationInterface::class);

        $ltiRegistrationCollection->add($ltiRegistrationMock);

        $this->ltiRegistrationRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->with('clientId')
            ->willReturn($ltiRegistrationCollection);

        $this->ltiRegistrationConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($ltiRegistrationMock)
            ->willReturn($registrationMock);

        $this->assertSame(
            $registrationMock,
            $this->registrationRepository->findByClientId('clientId')
        );
    }

    public function testFindByPlatformIssuer(): void
    {
        $ltiRegistrationMock = $this->createMock(LtiRegistration::class);
        $ltiRegistrationCollection = new LtiRegistrationCollection();
        $registrationMock = $this->createMock(RegistrationInterface::class);

        $ltiRegistrationCollection->add($ltiRegistrationMock);

        $this->ltiRegistrationRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->with('clientId', 'platformIssuer')
            ->willReturn($ltiRegistrationCollection);

        $this->ltiRegistrationConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($ltiRegistrationMock)
            ->willReturn($registrationMock);

        $this->assertSame(
            $registrationMock,
            $this->registrationRepository->findByPlatformIssuer('platformIssuer', 'clientId')
        );
    }

    public function testFindByToolIssuer(): void
    {
        $ltiRegistrationMock = $this->createMock(LtiRegistration::class);
        $ltiRegistrationCollection = new LtiRegistrationCollection();
        $registrationMock = $this->createMock(RegistrationInterface::class);

        $ltiRegistrationCollection->add($ltiRegistrationMock);

        $this->ltiRegistrationRepositoryMock
            ->expects($this->once())
            ->method('findAll')
            ->with('clientId', null, 'toolIssuer')
            ->willReturn($ltiRegistrationCollection);

        $this->ltiRegistrationConverterMock
            ->expects($this->once())
            ->method('convert')
            ->with($ltiRegistrationMock)
            ->willReturn($registrationMock);

        $this->assertSame(
            $registrationMock,
            $this->registrationRepository->findByToolIssuer('toolIssuer', 'clientId')
        );
    }
}
