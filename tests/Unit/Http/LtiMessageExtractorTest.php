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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Http;

use Nyholm\Psr7\ServerRequest;
use OAT\Library\EnvironmentManagementClient\Exception\LtiMessageExtractFailedException;
use OAT\Library\EnvironmentManagementClient\Http\LtiMessageExtractor;
use OAT\Library\Lti1p3Core\Message\Payload\MessagePayloadInterface;
use PHPUnit\Framework\TestCase;

final class LtiMessageExtractorTest extends TestCase
{
    private LtiMessageExtractor $subject;

    protected function setUp(): void
    {
        $this->subject = new LtiMessageExtractor;
    }

    public function testExtractLtiMessage(): void
    {
        $request = new ServerRequest('GET', 'http://example.test',
            [
                'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6InBsYXRmb3JtS2V5In0.eyJodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS9jbGFpbS9yZXNvdXJjZV9saW5rIjp7ImlkIjoiMDc0MWNkMWYtM2I0MS00N2ZiLWIyN2ItN2VmY2ZkYTRkMzAzIn0sImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL3ZlcnNpb24iOiIxLjMuMCIsImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL21lc3NhZ2VfdHlwZSI6Ikx0aVJlc291cmNlTGlua1JlcXVlc3QiLCJodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS9jbGFpbS9kZXBsb3ltZW50X2lkIjoiZGVwbG95bWVudElkMSIsImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL3RhcmdldF9saW5rX3VyaSI6Imh0dHA6Ly9sb2NhbGhvc3Q6MTAwMDAvYXBwIiwiaHR0cHM6Ly9wdXJsLmltc2dsb2JhbC5vcmcvc3BlYy9sdGkvY2xhaW0vcm9sZXMiOltdLCJyZWdpc3RyYXRpb25faWQiOiJhdXRoX3NlcnZlcl9yZWdpc3RyYXRpb24iLCJub25jZSI6ImM3YTIzNjUxLWQ3MDItNGJmZC1hMDI1LTllNDA5NTg3MGE2YiIsImp0aSI6IjcxZmFkYWVmLTZkMjQtNDIwZi1hYTJjLWUzYzc2NGY3MmE2NyIsImlhdCI6MTY0Mzk2MDUyMS41MDQ3MjMsIm5iZiI6MTY0Mzk2MDUyMS41MDQ3MjMsImV4cCI6MTY0Mzk2MTEyMS41MDQ3MjMsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwNy9wbGF0Zm9ybSIsImF1ZCI6ImNsaWVudC1hdXRoIn0.TZ09i9iDlV--uc2NjukO9UK03CHogl_4MluGWzhidfX2U_1vUVq0lnuU3nZK20aT-qNI0oq0hHa4-pvWub3mneB-J14aHpatUuPfNmnZ2MQcg92goB8PdgyiKVWH0xPArm6QjH7pwQZgBzNe2xEC1Afll1uN3wG6nqIeRxnZ9vff_geHmkFZw_bDL4W8gpee93VFW0CHEFdQ2SffqW7hkxRHJfwvHbwUSzhj_d0rJdJPeOil74ppegxmOoWtstFUeJZWUbBzfq3u0a1CKD2TEJg57JMokSrsrww1grL8YYnkHTr39DYJcb-F1JyiSuL716n1z5JPbayFRnwZuJgyEw'
            ]);

        $ltiMessage = $this->subject->extract($request);

        $this->assertEquals('http://localhost:10000/app', $ltiMessage->getTargetLinkUri());
        $this->assertEquals("1.3.0", $ltiMessage->getVersion());
        $this->assertEquals("LtiResourceLinkRequest", $ltiMessage->getMessageType());
        $this->assertEquals([], $ltiMessage->getRoles());
        $this->assertEquals("http://localhost:8007/platform", $ltiMessage->getClaim(MessagePayloadInterface::CLAIM_ISS));
        $this->assertEquals(["client-auth"], $ltiMessage->getClaim(MessagePayloadInterface::CLAIM_AUD));
        $this->assertNotEmpty($ltiMessage->getToken()->toString());
    }

    public function testExtractLtiMessageWillThrowErrorWhenRequestIsEmpty(): void
    {
        $this->expectException(LtiMessageExtractFailedException::class);
        $this->expectExceptionMessage('Not able to parse Lti message from JWT token');

        $this->subject->extract(new ServerRequest('GET', 'http://example.test'));
    }

}
