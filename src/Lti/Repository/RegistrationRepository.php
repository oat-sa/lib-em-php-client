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

namespace OAT\Library\EnvironmentManagementClient\Lti\Repository;

use OAT\Library\EnvironmentManagementClient\Converter\LtiRegistrationConverter;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Repository\LtiRegistrationRepositoryInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Registration\RegistrationRepositoryInterface;

class RegistrationRepository implements RegistrationRepositoryInterface
{
    private LtiRegistrationRepositoryInterface $ltiRegistrationRepository;
    private LtiRegistrationConverter $ltiRegistrationConverter;

    public function __construct(
        LtiRegistrationRepositoryInterface $ltiRegistrationRepository,
        LtiRegistrationConverter $ltiRegistrationConverter
    ) {
        $this->ltiRegistrationRepository = $ltiRegistrationRepository;
        $this->ltiRegistrationConverter = $ltiRegistrationConverter;
    }

    public function find(string $identifier): ?RegistrationInterface
    {
        return $this->ltiRegistrationConverter->convert(
            $this->ltiRegistrationRepository->find($identifier)
        );
    }

    public function findAll(): array
    {
        return array_map(function (LtiRegistration $registration) {
            return $this->ltiRegistrationConverter->convert($registration);
        }, $this->ltiRegistrationRepository->findAll()->all());
    }

    public function findByClientId(string $clientId): ?RegistrationInterface
    {
        return $this->ltiRegistrationConverter->convert(
            current($this->ltiRegistrationRepository->findAll($clientId)->all())
        );
    }

    public function findByPlatformIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        return $this->ltiRegistrationConverter->convert(
            current($this->ltiRegistrationRepository->findAll($clientId, $issuer)->all())
        );
    }

    public function findByToolIssuer(string $issuer, string $clientId = null): ?RegistrationInterface
    {
        return $this->ltiRegistrationConverter->convert(
            current($this->ltiRegistrationRepository->findAll($clientId, null, $issuer)->all())
        );
    }
}
