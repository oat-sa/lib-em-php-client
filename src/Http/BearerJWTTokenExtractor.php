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

namespace OAT\Library\EnvironmentManagementClient\Http;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use OAT\Library\EnvironmentManagementClient\Exception\EnvironmentManagementClientException;
use OAT\Library\EnvironmentManagementClient\Exception\TokenUnauthorizedException;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class BearerJWTTokenExtractor implements JWTTokenExtractorInterface
{
    /**
     * @throws TokenUnauthorizedException
     * @throws EnvironmentManagementClientException
     */
    public function extract(ServerRequestInterface $request): Token
    {
        if (false === $request->hasHeader('authorization')) {
            throw new TokenUnauthorizedException('Missing Authorization header');
        }

        $header = $request->getHeader('authorization');
        $jwt = trim((string)preg_replace('/^\s*Bearer\s/', '', $header[0]));

        try {
            return $this->createConfiguration()->parser()->parse($jwt);
        } catch (Throwable $exception) {
            throw new EnvironmentManagementClientException(
                sprintf('Cannot parse token: %s', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * No need to validate JWT in any way, since it is already done by the Envoy filter.
     */
    private function createConfiguration(): Configuration
    {
        return Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::empty()
        );
    }
}
