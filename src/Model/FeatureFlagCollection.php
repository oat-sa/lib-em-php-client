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

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Oat\Envmgmt\Common\FeatureFlag as ProtoFeatureFlag;
use Oat\Envmgmt\Common\FeatureFlagCollection as ProtoFeatureFlagCollection;
use OutOfBoundsException;
use Traversable;

final class FeatureFlagCollection implements IteratorAggregate, Countable
{
    /** @var FeatureFlag[] */
    private array $flags = [];

    public static function fromProtobuf(ProtoFeatureFlagCollection $protoCollection): self
    {
        $collection = new self();

        /** @var ProtoFeatureFlag $protoFlag */
        foreach ($protoCollection->getData() as $protoFlag) {
            $collection->flags[strtolower($protoFlag->getName())] = FeatureFlag::fromProtobuf($protoFlag);
        }

        return $collection;
    }

    public function add(string $name, string $value): self
    {
        $this->flags[strtolower($name)] = new FeatureFlag($name, $value);

        return $this;
    }

    public function has(string $flagName): bool
    {
        return array_key_exists(strtolower($flagName), $this->flags);
    }

    public function get(string $flagName): FeatureFlag
    {
        if (!$this->has($flagName)) {
            throw new OutOfBoundsException(sprintf('Flag %s does not exist.', $flagName));
        }

        return $this->flags[strtolower($flagName)];
    }

    /**
     * @return FeatureFlag[]
     */
    public function all(): array
    {
        return array_values($this->flags);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->flags);
    }

    public function count(): int
    {
        return count($this->flags);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }
}
