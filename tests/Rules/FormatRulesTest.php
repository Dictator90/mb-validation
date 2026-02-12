<?php

namespace MB\Validation\Tests\Rules;

use MB\Validation\Tests\ValidationTestCase;

class FormatRulesTest extends ValidationTestCase
{
    public function test_url_passes(): void
    {
        $this->assertPasses(['field' => 'https://example.com'], ['field' => 'url']);
    }

    public function test_url_fails(): void
    {
        $this->assertFails(['field' => 'not-a-url'], ['field' => 'url']);
    }

    public function test_uuid_passes(): void
    {
        if (!method_exists(\MB\Support\Str::class, 'isUuid')) {
            $this->markTestSkipped('Str::isUuid not available');
        }
        $this->assertPasses(['field' => '550e8400-e29b-41d4-a716-446655440000'], ['field' => 'uuid']);
    }

    public function test_uuid_fails(): void
    {
        if (!method_exists(\MB\Support\Str::class, 'isUuid')) {
            $this->markTestSkipped('Str::isUuid not available');
        }
        $this->assertFails(['field' => 'not-uuid'], ['field' => 'uuid']);
    }

    public function test_ulid_passes(): void
    {
        if (!method_exists(\MB\Support\Str::class, 'isUlid')) {
            $this->markTestSkipped('Str::isUlid not available');
        }
        $this->assertPasses(['field' => '01ARZ3NDEKTSV4RRFFQ69G5FAV'], ['field' => 'ulid']);
    }

    public function test_ulid_fails(): void
    {
        if (!method_exists(\MB\Support\Str::class, 'isUlid')) {
            $this->markTestSkipped('Str::isUlid not available');
        }
        $this->assertFails(['field' => 'not-ulid'], ['field' => 'ulid']);
    }

    public function test_ip_passes(): void
    {
        $this->assertPasses(['field' => '192.168.1.1'], ['field' => 'ip']);
    }

    public function test_ip_fails(): void
    {
        $this->assertFails(['field' => '999.999.999.999'], ['field' => 'ip']);
    }

    public function test_ipv4_passes(): void
    {
        $this->assertPasses(['field' => '192.168.1.1'], ['field' => 'ipv4']);
    }

    public function test_ipv6_passes(): void
    {
        $this->assertPasses(['field' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'], ['field' => 'ipv6']);
    }

    public function test_json_passes(): void
    {
        $this->assertPasses(['field' => '{"key":"value"}'], ['field' => 'json']);
    }

    public function test_json_fails(): void
    {
        $this->assertFails(['field' => 'not json'], ['field' => 'json']);
    }

    public function test_hex_color_passes(): void
    {
        $this->assertPasses(['field' => '#ffffff'], ['field' => 'hex_color']);
    }

    public function test_hex_color_fails(): void
    {
        $this->assertFails(['field' => 'red'], ['field' => 'hex_color']);
    }

    public function test_timezone_passes(): void
    {
        $this->assertPasses(['field' => 'Europe/London'], ['field' => 'timezone']);
    }

    public function test_timezone_fails(): void
    {
        $this->assertFails(['field' => 'Invalid/Timezone'], ['field' => 'timezone']);
    }

    public function test_mac_address_passes(): void
    {
        $this->assertPasses(['field' => '00:1B:44:11:3A:B7'], ['field' => 'mac_address']);
    }

    public function test_mac_address_fails(): void
    {
        $this->assertFails(['field' => 'invalid'], ['field' => 'mac_address']);
    }
}
