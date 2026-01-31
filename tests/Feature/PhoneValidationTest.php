<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhoneValidationTest extends TestCase
{
    /**
     * Test yemeni_phone validation rule with various formats.
     *
     * @dataProvider phoneDataProvider
     */
    public function test_yemeni_phone_validation($phone, $expected)
    {
        $validator = Validator::make(['phone' => $phone], [
            'phone' => 'yemeni_phone'
        ]);

        $this->assertEquals($expected, $validator->passes(), "Validation failed for phone: $phone");
    }

    public static function phoneDataProvider()
    {
        return [
            // Valid formats - Sabafon (71)
            ['710000000', true],
            ['0710000000', true],
            ['967710000000', true],
            ['+967710000000', true],
            ['00967710000000', true],

            // Valid formats - Yemen Mobile (77)
            ['770000000', true],
            ['0770000000', true],
            ['+967770000000', true],

            // Valid formats - MTN/You (73)
            ['730000000', true],
            ['0730000000', true],

            // Valid formats - Way (70)
            ['700000000', true],
            ['0700000000', true],

            // Valid formats - Frog (78)
            ['780000000', true],
            ['0780000000', true],

            // Invalid formats
            ['123456789', false], // Invalid prefix
            ['77000000', false],  // Too short
            ['7700000000', false], // Too long
            ['abc770000000', false], // Non-numeric
            ['+966770000000', false], // Wrong country code
        ];
    }
}
