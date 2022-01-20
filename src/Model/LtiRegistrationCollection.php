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
use Oat\Envmgmt\Common\LtiRegistration as ProtoLtiRegistration;
use Oat\Envmgmt\Common\LtiRegistrationCollection as ProtoLtiRegistrationCollection;
use OutOfBoundsException;
use Traversable;

final class LtiRegistrationCollection implements IteratorAggregate, Countable
{
    /** @var LtiRegistration[] */
    private array $registrations = [];

    public static function fromProtobuf(ProtoLtiRegistrationCollection $protoCollection): self
    {
        $collection = new self();

        /** @var ProtoLtiRegistration $protoRegistration */
        foreach ($protoCollection->getData() as $protoRegistration) {
            $collection->add(LtiRegistration::fromProtobuf($protoRegistration));
        }

        return $collection;
    }

    public function add(LtiRegistration $registration): self
    {
        $this->registrations[$registration->getId()] = $registration;

        return $this;
    }

    public function has(string $registrationId): bool
    {
        return array_key_exists($registrationId, $this->registrations);
    }

    public function get(string $registrationId): LtiRegistration
    {
        if (!$this->has($registrationId)) {
            throw new OutOfBoundsException(sprintf('LTI Registration %s does not exist.', $registrationId));
        }

        return $this->registrations[$registrationId];
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->registrations);
    }

    public function count(): int
    {
        return count($this->registrations);
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @return LtiRegistration[]
     */
    public function all(): array
    {
        return array_values($this->registrations);
    }
}
