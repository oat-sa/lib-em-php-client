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
use Oat\Envmgmt\Common\Configuration as ProtoConfiguration;
use Oat\Envmgmt\Common\ConfigurationCollection as ProtoConfigurationCollection;
use OutOfBoundsException;
use Traversable;

class ConfigurationCollection implements IteratorAggregate, Countable
{
    /** @var Configuration[] */
    private array $configs = [];

    public static function fromProtobuf(ProtoConfigurationCollection $protoCollection): self
    {
        $collection = new self();

        /** @var ProtoConfiguration $protoConfig */
        foreach ($protoCollection->getData() as $protoConfig) {
            $collection->add(Configuration::fromProtobuf($protoConfig));
        }

        return $collection;
    }

    public function add(Configuration $configuration): self
    {
        $this->configs[$configuration->getName()] = $configuration;

        return $this;
    }

    public function has(string $configName): bool
    {
        return array_key_exists($configName, $this->configs);
    }

    public function get(string $configName): Configuration
    {
        if (!$this->has($configName)) {
            throw new OutOfBoundsException(sprintf('Configuration %s does not exist.', $configName));
        }

        return $this->configs[$configName];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->configs);
    }

    public function count(): int
    {
        return count($this->configs);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @return Configuration[]
     */
    public function all(): array
    {
        return array_values($this->configs);
    }
}
