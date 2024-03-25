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

use Oat\Envmgmt\Common\LtiKeyChain as ProtoLtiKeyChain;

class LtiKeyChain
{
    private string $id;
    private string $keySetName;
    private string $publicKey;
    private string $privateKey;
    private string $privateKeyPassphrase;

    public function __construct(
        string $id,
        string $keySetName,
        string $publicKey,
        string $privateKey,
        string $privateKeyPassphrase
    ) {
        $this->id = $id;
        $this->keySetName = $keySetName;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
        $this->privateKeyPassphrase = $privateKeyPassphrase;
    }

    public static function fromProtobuf(ProtoLtiKeyChain $protoChain): self
    {
        return new self(
            $protoChain->getId(),
            $protoChain->getKeySetName(),
            $protoChain->getPublicKey(),
            $protoChain->getPrivateKey(),
            $protoChain->getPrivateKeyPassphrase(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getKeySetName(): string
    {
        return $this->keySetName;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getPrivateKeyPassphrase(): string
    {
        return $this->privateKeyPassphrase;
    }
}
