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

namespace OAT\Library\EnvironmentManagementClient\Model;

use InvalidArgumentException;
use Oat\Envmgmt\Common\Oauth2User as ProtoOauth2User;

class OAuth2User
{
    private string $username;
    private string $password;
    private array $roles;

    public function __construct(
        string $username,
        string $password,
        array $roles,
    ) {
        if (count(array_filter($roles, static function($value) {
            return !is_string($value);
        }))) {
            throw new InvalidArgumentException('roles must be string');
        }

        $this->username = $username;
        $this->password = $password;
        $this->roles = $roles;
    }

    public static function fromProtobuf(ProtoOauth2User $protoOauth2User): self
    {
        return new self(
            $protoOauth2User->getUsername(),
            $protoOauth2User->getPassword(),
            iterator_to_array($protoOauth2User->getRoles()),
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
