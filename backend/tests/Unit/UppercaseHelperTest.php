<?php

namespace Tests\Unit;

use App\Helpers\UppercaseHelper;
use PHPUnit\Framework\TestCase;

class UppercaseHelperTest extends TestCase
{
    /** @test */
    public function it_converts_a_string_to_uppercase(): void
    {
        $this->assertSame('HELLO WORLD', UppercaseHelper::convert('hello world'));
    }

    /** @test */
    public function it_returns_null_for_null_input(): void
    {
        $this->assertNull(UppercaseHelper::convert(null));
    }

    /** @test */
    public function it_handles_already_uppercase_strings(): void
    {
        $this->assertSame('ALREADY', UppercaseHelper::convert('ALREADY'));
    }

    /** @test */
    public function it_handles_mixed_case(): void
    {
        $this->assertSame('JOHN DOE', UppercaseHelper::convert('John Doe'));
    }

    /** @test */
    public function it_handles_empty_string(): void
    {
        $this->assertSame('', UppercaseHelper::convert(''));
    }

    /** @test */
    public function it_handles_unicode_characters(): void
    {
        $this->assertSame('MÜNCHEN', UppercaseHelper::convert('münchen'));
    }

    /** @test */
    public function it_converts_array_values_to_uppercase(): void
    {
        $input = [
            'name'  => 'john doe',
            'city'  => 'new york',
            'email' => 'John@Example.com',
        ];

        $result = UppercaseHelper::convertArray($input, ['email']);

        $this->assertSame('JOHN DOE', $result['name']);
        $this->assertSame('NEW YORK', $result['city']);
        $this->assertSame('John@Example.com', $result['email']);
    }

    /** @test */
    public function it_skips_exception_fields_in_array(): void
    {
        $input = [
            'username' => 'AdminUser',
            'password' => 'Secret123!',
            'department' => 'engineering',
        ];

        $except = ['username', 'password'];
        $result = UppercaseHelper::convertArray($input, $except);

        $this->assertSame('AdminUser', $result['username']);
        $this->assertSame('Secret123!', $result['password']);
        $this->assertSame('ENGINEERING', $result['department']);
    }

    /** @test */
    public function it_handles_nested_arrays(): void
    {
        $input = [
            'employee' => [
                'name' => 'jane',
                'email' => 'jane@corp.com',
            ],
        ];

        $result = UppercaseHelper::convertArray($input, ['email']);

        $this->assertSame('JANE', $result['employee']['name']);
        $this->assertSame('jane@corp.com', $result['employee']['email']);
    }

    /** @test */
    public function it_preserves_numeric_and_special_characters(): void
    {
        $this->assertSame('APT 12-B, FLOOR #3', UppercaseHelper::convert('apt 12-b, floor #3'));
    }
}
