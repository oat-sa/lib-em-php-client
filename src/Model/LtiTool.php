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

use Oat\Envmgmt\Common\LtiTool as ProtoLtiTool;

final class LtiTool
{
    private string $id;
    private string $name;
    private string $audience;
    private string $oidcInitiationUrl;
    private string $launchUrl;
    private string $deepLinkingUrl;

    public function __construct(
        string $id,
        string $name,
        string $audience,
        string $oidcInitiationUrl,
        string $launchUrl,
        string $deepLinkingUrl
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->audience = $audience;
        $this->oidcInitiationUrl = $oidcInitiationUrl;
        $this->launchUrl = $launchUrl;
        $this->deepLinkingUrl = $deepLinkingUrl;
    }

    public static function fromProtobuf(ProtoLtiTool $protoTool): self
    {
        return new LtiTool(
            $protoTool->getId(),
            $protoTool->getName(),
            $protoTool->getAudience(),
            $protoTool->getOidcInitiationUrl(),
            $protoTool->getLaunchUrl(),
            $protoTool->getDeepLinkingUrl(),
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAudience(): string
    {
        return $this->audience;
    }

    public function getOidcInitiationUrl(): string
    {
        return $this->oidcInitiationUrl;
    }

    public function getLaunchUrl(): string
    {
        return $this->launchUrl;
    }

    public function getDeepLinkingUrl(): string
    {
        return $this->deepLinkingUrl;
    }
}
