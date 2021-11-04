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

use OAT\Library\EnvironmentManagementClient\Exception\TenantIdNotFoundException;
use Psr\Http\Message\MessageInterface;

final class TenantIdHeaderExtractor implements TenantIdExtractorInterface
{
    private const DEFAULT_HEADER_NAME = 'X-Oat-Tenant-Id';

    private string $headerPrefix;

    public function __construct(string $headerPrefix = self::DEFAULT_HEADER_NAME)
    {
        $this->headerPrefix = $headerPrefix;
    }

    public function extract(MessageInterface $message): string
    {
        $headers = $message->getHeaders();

        foreach ($headers as $headerName => $headerValues) {
            if (strtolower($headerName) === strtolower($this->headerPrefix)) {
                return array_pop($headerValues);
            }
        }

        throw TenantIdNotFoundException::notInHeader();
    }
}
