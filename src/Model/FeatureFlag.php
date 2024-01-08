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

namespace OAT\Library\EnvironmentManagementClient\Model;

use InvalidArgumentException;
use Oat\Envmgmt\Common\FeatureFlag as ProtoFeatureFlag;

class FeatureFlag
{
    private string $name;
    private string $value;

    public function __construct(string $name, string $value)
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Flag name cannot be empty.');
        }

        if ('' === $value) {
            throw new InvalidArgumentException('Flag value cannot be empty.');
        }

        $this->name = $name;
        $this->value = $value;
    }

    public static function fromProtobuf(ProtoFeatureFlag $protoFlag): self
    {
        return new self($protoFlag->getName(), $protoFlag->getValue());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
