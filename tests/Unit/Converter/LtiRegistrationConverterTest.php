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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Converter;

use InvalidArgumentException;
use OAT\Library\EnvironmentManagementClient\Converter\LtiPlatformConverter;
use OAT\Library\EnvironmentManagementClient\Converter\LtiRegistrationConverter;
use OAT\Library\EnvironmentManagementClient\Converter\LtiToolConverter;
use OAT\Library\EnvironmentManagementClient\Model\LtiKeyChain;
use OAT\Library\EnvironmentManagementClient\Model\LtiPlatform;
use OAT\Library\EnvironmentManagementClient\Model\LtiRegistration;
use OAT\Library\EnvironmentManagementClient\Model\LtiTool;
use OAT\Library\Lti1p3Core\Platform\Platform;
use OAT\Library\Lti1p3Core\Registration\Registration;
use OAT\Library\Lti1p3Core\Registration\RegistrationInterface;
use OAT\Library\Lti1p3Core\Security\Key\Key;
use OAT\Library\Lti1p3Core\Security\Key\KeyChain;
use OAT\Library\Lti1p3Core\Security\Key\KeyChainFactory;
use OAT\Library\Lti1p3Core\Tool\Tool;
use PHPUnit\Framework\TestCase;

class LtiRegistrationConverterTest extends TestCase
{
    private LtiRegistrationConverter $subject;

    protected function setUp(): void
    {
        $ltiPlatformConverter = new LtiPlatformConverter();
        $ltiToolConverter = new LtiToolConverter();
        $keyChainFactory = new KeyChainFactory();

        $this->subject = new LtiRegistrationConverter(
            $ltiPlatformConverter,
            $ltiToolConverter,
            $keyChainFactory,
        );
    }

    /**
     * @dataProvider ltiRegistrationDataProvider
     */
    public function testConvert(LtiRegistration $registration, ?Registration $expectedRegistration, ?string $expectedExceptionMessage): void
    {
        if ($expectedExceptionMessage !== null) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }
        $convertedLtiRegistration = $this->subject->convert($registration);

        if ($expectedExceptionMessage === null) {
            $this->assertRegistrations($expectedRegistration, $convertedLtiRegistration);
        }
    }

    /**
     * @dataProvider ltiRegistrationDataProvider
     */
    public function testConvertWithTenantId(LtiRegistration $registration, ?Registration $expectedRegistration, ?string $expectedExceptionMessage): void
    {
        if ($expectedExceptionMessage !== null) {
            $this->expectException(InvalidArgumentException::class);
            $this->expectExceptionMessage($expectedExceptionMessage);
        }
        $convertedLtiRegistration = $this->subject->convertWithTenantId($registration);

        if ($expectedExceptionMessage === null) {
            $this->assertRegistrations($expectedRegistration, $convertedLtiRegistration);
            $this->assertSame('tenantId', $convertedLtiRegistration->getTenantIdentifier());
        }
    }

    public function ltiRegistrationDataProvider(): array
    {
        return [
            'everything is defined' => [
                $this->createLtiRegistration(),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    new KeyChain(
                        'platformId',
                        'platformKeySetName',
                        new Key('platformPublicKey'),
                        new Key('platformPrivateKey', 'platformPrivateKeyPassphrase'),
                    ),
                    new KeyChain(
                        'toolId',
                        'toolKeySetName',
                        new Key('toolPublicKey'),
                        new Key('toolPrivateKey', 'toolPrivateKeyPassphrase'),
                    ),
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'platform is not defined' => [
                $this->createLtiRegistration(hasPlatform: false),
                null,
                'LTI Platform not returned for Registration id',
            ],
            'tool is not defined' => [
                $this->createLtiRegistration(hasTool: false),
                null,
                'LTI Tool not returned for Registration id',
            ],
            'platform keychain is not defined' => [
                $this->createLtiRegistration(hasPlatformKeyChain: false),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    null,
                    new KeyChain(
                        'toolId',
                        'toolKeySetName',
                        new Key('toolPublicKey'),
                        new Key('toolPrivateKey', 'toolPrivateKeyPassphrase'),
                    ),
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'platform keychain key set name is not defined' => [
                $this->createLtiRegistration(platformKeyChainKeySetName: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    null,
                    new KeyChain(
                        'toolId',
                        'toolKeySetName',
                        new Key('toolPublicKey'),
                        new Key('toolPrivateKey', 'toolPrivateKeyPassphrase'),
                    ),
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'platform keychain public key is not defined' => [
                $this->createLtiRegistration(platformKeyChainPublicKey: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    null,
                    new KeyChain(
                        'toolId',
                        'toolKeySetName',
                        new Key('toolPublicKey'),
                        new Key('toolPrivateKey', 'toolPrivateKeyPassphrase'),
                    ),
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'platform keychain private key is not defined' => [
                $this->createLtiRegistration(platformKeyChainPrivateKey: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    null,
                    new KeyChain(
                        'toolId',
                        'toolKeySetName',
                        new Key('toolPublicKey'),
                        new Key('toolPrivateKey', 'toolPrivateKeyPassphrase'),
                    ),
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'tool keychain is not defined' => [
                $this->createLtiRegistration(hasToolKeyChain: false),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    new KeyChain(
                        'platformId',
                        'platformKeySetName',
                        new Key('platformPublicKey'),
                        new Key('platformPrivateKey', 'platformPrivateKeyPassphrase'),
                    ),
                    null,
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'tool keychain key set name is not defined' => [
                $this->createLtiRegistration(toolKeyChainKeySetName: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    new KeyChain(
                        'platformId',
                        'platformKeySetName',
                        new Key('platformPublicKey'),
                        new Key('platformPrivateKey', 'platformPrivateKeyPassphrase'),
                    ),
                    null,
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'tool keychain public key is not defined' => [
                $this->createLtiRegistration(toolKeyChainPublicKey: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    new KeyChain(
                        'platformId',
                        'platformKeySetName',
                        new Key('platformPublicKey'),
                        new Key('platformPrivateKey', 'platformPrivateKeyPassphrase'),
                    ),
                    null,
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
            'tool keychain private key is not defined' => [
                $this->createLtiRegistration(toolKeyChainPrivateKey: ''),
                new Registration(
                    'id',
                    'clientId',
                    new Platform(
                        'id',
                        'name',
                        'audience',
                        'oidcAuthenticationUrl',
                        'oauth2AccessTokenUrl',
                    ),
                    new Tool(
                        'id',
                        'name',
                        'audience',
                        'oidcInitiationUrl',
                        'launchUrl',
                        'deepLinkingUrl',
                    ),
                    ['1', '2', '3'],
                    new KeyChain(
                        'platformId',
                        'platformKeySetName',
                        new Key('platformPublicKey'),
                        new Key('platformPrivateKey', 'platformPrivateKeyPassphrase'),
                    ),
                    null,
                    'platformJwksUrl',
                    'toolJwksUrl'
                ),
                null
            ],
        ];
    }
    
    private function createLtiRegistration(
        string $id = 'id',
        string $clientId = 'clientId',
        string $platformId = 'platformId',
        string $toolId = 'toolId',
        array $deploymentIds = ['1', '2', '3'],
        string $platformJwksUrl = 'platformJwksUrl',
        string $toolJwksUrl = 'toolJwksUrl',
        bool $hasPlatformKeyChain = true,
        string $platformKeyChainId = 'platformId',
        string $platformKeyChainKeySetName = 'platformKeySetName',
        string $platformKeyChainPublicKey = 'platformPublicKey',
        string $platformKeyChainPrivateKey = 'platformPrivateKey',
        string $platformKeyChainPrivateKeyPassphrase = 'platformPrivateKeyPassphrase',
        bool $hasToolKeyChain = true,
        string $toolKeyChainId = 'toolId',
        string $toolKeyChainKeySetName = 'toolKeySetName',
        string $toolKeyChainPublicKey = 'toolPublicKey',
        string $toolKeyChainPrivateKey = 'toolPrivateKey',
        string $toolKeyChainPrivateKeyPassphrase = 'toolPrivateKeyPassphrase',
        bool $hasPlatform = true,
        string $platformIdentifier = 'id',
        string $platformName = 'name',
        string $platformAudience = 'audience',
        string $platformOidcAuthenticationUrl = 'oidcAuthenticationUrl',
        string $platformOauth2AccessTokenUrl = 'oauth2AccessTokenUrl',
        bool $hasTool = true,
        string $toolIdentifier = 'id',
        string $toolName = 'name',
        string $toolAudience = 'audience',
        string $toolOidcInitiationUrl = 'oidcInitiationUrl',
        string $toolLaunchUrl = 'launchUrl',
        string $toolDeepLinkingUrl = 'deepLinkingUrl',
        string $tenantId = 'tenantId',
    ): LtiRegistration {
        return new LtiRegistration(
            $id,
            $clientId,
            $platformId,
            $toolId,
            $deploymentIds,
            $platformJwksUrl,
            $toolJwksUrl,
            $hasPlatformKeyChain ? new LtiKeyChain(
                $platformKeyChainId,
                $platformKeyChainKeySetName,
                $platformKeyChainPublicKey,
                $platformKeyChainPrivateKey,
                $platformKeyChainPrivateKeyPassphrase,
            ) : null,
            $hasToolKeyChain ? new LtiKeyChain(
                $toolKeyChainId,
                $toolKeyChainKeySetName,
                $toolKeyChainPublicKey,
                $toolKeyChainPrivateKey,
                $toolKeyChainPrivateKeyPassphrase,
            ) : null,
            $hasPlatform ? new LtiPlatform(
                $platformIdentifier,
                $platformName,
                $platformAudience,
                $platformOidcAuthenticationUrl,
                $platformOauth2AccessTokenUrl,
            ) : null,
            $hasTool ? new LtiTool(
                $toolIdentifier,
                $toolName,
                $toolAudience,
                $toolOidcInitiationUrl,
                $toolLaunchUrl,
                $toolDeepLinkingUrl,
            ) : null,
            $tenantId
        );
    }

    private function assertRegistrations(RegistrationInterface $expectedRegistration, RegistrationInterface $convertedLtiRegistration): void
    {
        $this->assertSame($expectedRegistration->getIdentifier(), $convertedLtiRegistration->getIdentifier());
        $this->assertSame($expectedRegistration->getClientId(), $convertedLtiRegistration->getClientId());
        $this->assertSame($expectedRegistration->getDeploymentIds(), $convertedLtiRegistration->getDeploymentIds());
        $this->assertSame($expectedRegistration->getPlatformJwksUrl(), $convertedLtiRegistration->getPlatformJwksUrl());
        $this->assertSame($expectedRegistration->getToolJwksUrl(), $convertedLtiRegistration->getToolJwksUrl());
        $this->assertSame($expectedRegistration->getPlatform()->getIdentifier(), $convertedLtiRegistration->getPlatform()->getIdentifier());
        $this->assertSame($expectedRegistration->getPlatform()->getName(), $convertedLtiRegistration->getPlatform()->getName());
        $this->assertSame($expectedRegistration->getPlatform()->getAudience(), $convertedLtiRegistration->getPlatform()->getAudience());
        $this->assertSame($expectedRegistration->getPlatform()->getOidcAuthenticationUrl(), $convertedLtiRegistration->getPlatform()->getOidcAuthenticationUrl());
        $this->assertSame($expectedRegistration->getPlatform()->getOAuth2AccessTokenUrl(), $convertedLtiRegistration->getPlatform()->getOAuth2AccessTokenUrl());
        $this->assertSame($expectedRegistration->getTool()->getIdentifier(), $convertedLtiRegistration->getTool()->getIdentifier());
        $this->assertSame($expectedRegistration->getTool()->getName(), $convertedLtiRegistration->getTool()->getName());
        $this->assertSame($expectedRegistration->getTool()->getAudience(), $convertedLtiRegistration->getTool()->getAudience());
        $this->assertSame($expectedRegistration->getTool()->getOidcInitiationUrl(), $convertedLtiRegistration->getTool()->getOidcInitiationUrl());
        $this->assertSame($expectedRegistration->getTool()->getLaunchUrl(), $convertedLtiRegistration->getTool()->getLaunchUrl());
        $this->assertSame($expectedRegistration->getTool()->getDeepLinkingUrl(), $convertedLtiRegistration->getTool()->getDeepLinkingUrl());
        $this->assertSame($expectedRegistration->getPlatformKeyChain()?->getIdentifier(), $convertedLtiRegistration->getPlatformKeyChain()?->getIdentifier());
        $this->assertSame($expectedRegistration->getPlatformKeyChain()?->getKeySetName(), $convertedLtiRegistration->getPlatformKeyChain()?->getKeySetName());
        $this->assertSame($expectedRegistration->getPlatformKeyChain()?->getPublicKey()->getContent(), $convertedLtiRegistration->getPlatformKeyChain()?->getPublicKey()->getContent());
        $this->assertSame($expectedRegistration->getPlatformKeyChain()?->getPublicKey()->getAlgorithm(), $convertedLtiRegistration->getPlatformKeyChain()?->getPublicKey()->getAlgorithm());
        $this->assertSame($expectedRegistration->getPlatformKeyChain()?->getPublicKey()->getPassPhrase(), $convertedLtiRegistration->getPlatformKeyChain()?->getPublicKey()->getPassPhrase());
        $this->assertSame($expectedRegistration->getToolKeyChain()?->getIdentifier(), $convertedLtiRegistration->getToolKeyChain()?->getIdentifier());
        $this->assertSame($expectedRegistration->getToolKeyChain()?->getKeySetName(), $convertedLtiRegistration->getToolKeyChain()?->getKeySetName());
        $this->assertSame($expectedRegistration->getToolKeyChain()?->getPublicKey()->getContent(), $convertedLtiRegistration->getToolKeyChain()?->getPublicKey()->getContent());
        $this->assertSame($expectedRegistration->getToolKeyChain()?->getPublicKey()->getAlgorithm(), $convertedLtiRegistration->getToolKeyChain()?->getPublicKey()->getAlgorithm());
        $this->assertSame($expectedRegistration->getToolKeyChain()?->getPublicKey()->getPassPhrase(), $convertedLtiRegistration->getToolKeyChain()?->getPublicKey()->getPassPhrase());
    }
}
