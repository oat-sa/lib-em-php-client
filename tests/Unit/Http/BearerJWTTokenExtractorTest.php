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

namespace OAT\Library\EnvironmentManagementClient\Tests\Unit\Http;

use Nyholm\Psr7\Factory\Psr17Factory;
use OAT\Library\EnvironmentManagementClient\Exception\EnvironmentManagementClientException;
use OAT\Library\EnvironmentManagementClient\Http\BearerJWTTokenExtractor;
use PHPUnit\Framework\TestCase;

final class BearerJWTTokenExtractorTest extends TestCase
{
    private BearerJWTTokenExtractor $subject;

    protected function setUp(): void
    {
        $this->subject = new BearerJWTTokenExtractor();
    }

    public function testItThrowsExceptionWhenAuthorizationHeaderMissing(): void
    {
        $this->expectException(EnvironmentManagementClientException::class);
        $this->expectExceptionMessage('Missing Authorization header');

        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');
        $this->subject->extract($request);
    }

    public function testItThrowsExceptionWhenInvalidJWTProvided(): void
    {
        $this->expectException(EnvironmentManagementClientException::class);
        $this->expectExceptionMessage('Cannot parse token: The JWT string must have two dots');

        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');
        $request = $request->withHeader('Authorization', 'Bearer NOT-VALID-JWT');

        $this->subject->extract($request);
    }

    public function testJWTTokenParsedSuccessfully(): void
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6InByaW1hcnlLZXlQYWlyIn0.eyJodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS9jbGFpbS9yZXNvdXJjZV9saW5rIjp7ImlkIjoiMTVhOGZkMjUtN2NlZi00ODBkLThhNTQtZjZjZGE1ZjM1MWE0In0sImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL3ZlcnNpb24iOiIxLjMuMCIsImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL21lc3NhZ2VfdHlwZSI6Ikx0aVJlc291cmNlTGlua1JlcXVlc3QiLCJodHRwczovL3B1cmwuaW1zZ2xvYmFsLm9yZy9zcGVjL2x0aS9jbGFpbS9kZXBsb3ltZW50X2lkIjoiZGVwbG95bWVudElkMSIsImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL3RhcmdldF9saW5rX3VyaSI6Imh0dHA6Ly9kZXZraXQtbHRpMXAzLmxvY2FsaG9zdC90b29sL2xhdW5jaCIsImh0dHBzOi8vcHVybC5pbXNnbG9iYWwub3JnL3NwZWMvbHRpL2NsYWltL3JvbGVzIjpbXSwicmVnaXN0cmF0aW9uX2lkIjoiYXV0aFNlcnZlciIsImlzcyI6ImxvY2FsaG9zdDo4MDA1IiwiYXVkIjoiY2xpZW50LWF1dGgiLCJzdWIiOiJjM3BvIiwibmFtZSI6IkMzUE8iLCJlbWFpbCI6ImMzcG9AcmViZWxzLmNvbSIsImdpdmVuX25hbWUiOiJDM1BPIiwibG9jYWxlIjoiZW4iLCJwaWN0dXJlIjoiaHR0cHM6Ly9jZG40Lmljb25maW5kZXIuY29tL2RhdGEvaWNvbnMvZmFtb3VzLWNoYXJhY3RlcnMtYWRkLW9uLXZvbC0xLWZsYXQvNDgvRmFtb3VzX0NoYXJhY3Rlcl8tX0FkZF9Pbl8xLTM0LTUxMi5wbmciLCJ0ZW5hbnRfaWQiOiJhY2MtMS5pbnMtMSIsImp0aSI6ImUzYTA0YWNkLTMwOWYtNDBjNy05MTE0LTczOGEzYjliYTA0NyIsImlhdCI6MTYzNDgyMjQyMi44NTM3NTUsIm5iZiI6MTYzNDgyMjQyMi44NTM3NTUsImV4cCI6MTYzNDgyMzAyMi44NTM3NTV9.oPq5TssKXV0IWcQh-nZwcTgRcK1D7tMT3mX-kA5CIYNz03ra1UV8I8hQ4fPTDzv_9hUEKw3n5saVsErg9bbqsRNOiOw9-GzfZOKANoCNkeY37Cs6gY5npe6_vCoUFQWqKjYxXaPOTppQwU_Ujwd2rfPranX5LZKQg7DmN1Fv3mZmIbeyqOhMsO8VQL4hNN81m49inWisc-jIqa2gxNO70xVUJ-BuJ9NpakSz7Y83n1vJUQX-1IVfrVqcbV5jFKlHCo90ofUoQ0TqLwd1xBCCzAwqrzF1NcYYhPpvrI8vyIWgwGDHPLRXi_NyrrlTX9b2m77nf1LSplsfcYqAiqj4RqmXpenONFq-9Ewj3ZGCeib_YVzynEFcuMq8hIYJxr68eR-XkisKQ_fABvt7_yPIKnoD3TcCjxtZCbw5_5yceygJk9UEUKlYvZw16Ex9ymjQPGrcRKHFxqQva4emxpgwY2hwV4eYHQGNaNslqFdHhpa58GL9fH9A4pw1xiVPY2-q_Sje_63H3zPC2Q2O28VxYJcaNEPXYc4nmH5sAL0jqmhRT-niV_PJ1IKdpVZoo-p_0vABR6seGBjtpT_bQVRfVfw1xXr2Su1RPTqPSd9qyfKQauR07XnXtzrZwwXkHlAbvpz6XtsFWFfVxXn9UnwhO8rznTDKD8SOKTt_b_npyx0';
        $request = (new Psr17Factory())->createServerRequest('GET', 'http://example.test');
        $request = $request->withHeader('Authorization', sprintf('Bearer %s', $token));

        $token = $this->subject->extract($request);

        $this->assertNotEmpty($token->headers()->all());
        $this->assertNotEmpty($token->claims()->all());
    }
}
