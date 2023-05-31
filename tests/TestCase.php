<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public const UUID_PATTERN_REGEX = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';

    public function assertIsValidUuid($uuid, string $message = '')
    {
        $this->assertRegExp(self::UUID_PATTERN_REGEX, $uuid, $message);
    }
}
