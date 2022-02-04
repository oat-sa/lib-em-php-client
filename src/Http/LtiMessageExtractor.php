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

namespace OAT\Library\EnvironmentManagementClient\Http;

use OAT\Library\EnvironmentManagementClient\Exception\LtiMessageExtractFailedException;
use OAT\Library\Lti1p3Core\Security\Jwt\Token;
use Psr\Http\Message\ServerRequestInterface;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayload;
use OAT\Library\Lti1p3Core\Message\Payload\LtiMessagePayloadInterface;

final class LtiMessageExtractor implements LtiMessageExtractorInterface
{
    private JWTTokenExtractorInterface $tokenExtractor;

    public function __construct(JWTTokenExtractorInterface $tokenExtractor = null)
    {
        $this->tokenExtractor = $tokenExtractor ?? new BearerJWTTokenExtractor();
    }

    public function extract(ServerRequestInterface $request): LtiMessagePayloadInterface
    {
        if (!empty($request->hasHeader("Authorization"))) {
            $token = $this->tokenExtractor->extract($request);

            return new LtiMessagePayload(new Token($token));
        }

        throw LtiMessageExtractFailedException::unableToExtractLtiMessage();
    }
}
