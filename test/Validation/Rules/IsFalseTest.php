<?php

declare(strict_types=1);

namespace Equit\Test\Validation\Rules;

use Equit\Validation\Rule;
use Equit\Validation\Rules\IsFalse;
use Generator;
use Equit\Test\Framework\RuleTestCase;
use TypeError;

/**
 * Test case for the IsFalse validator rule.
 */
class IsFalseTest extends RuleTestCase
{
    /**
     * @inheritDoc
     */
    protected function ruleInstance(): Rule
    {
        return new IsFalse();
    }

    /**
     * Data provider for testPasses().
     *
     * @return \Generator The test data.
     */
    public function dataForTestPasses(): Generator
    {
        yield from [
            "typicalFalse" => ["field", false, true,],
            "typicalInt0" => ["field", 0, true,],
            "typicalStringOff" => ["field", "off", true,],
            "typicalStringNo" => ["field", "no", true,],
            "typicalStringFalse" => ["field", "false", true,],
            "typicalStringOffUpper" => ["field", "OFF", true,],
            "typicalStringNoUpper" => ["field", "NO", true,],
            "typicalStringFalseUpper" => ["field", "FALSE", true,],
            "typicalTrue" => ["field", true, false,],
            "typicalInt1" => ["field", 1, false,],
            "typicalStringOn" => ["field", "on", false,],
            "typicalStringYes" => ["field", "yes", false,],
            "typicalStringTrue" => ["field", "true", false,],
            "typicalStringOnUpper" => ["field", "ON", false,],
            "typicalStringYesUpper" => ["field", "YES", false,],
            "typicalStringTrueUpper" => ["field", "TRUE", false,],
            "typicalEmptyString" => ["field", "", false,],
            "extremeInt2" => ["field", 2, false,],
            "extremeInt-1" => ["field", -1, false,],
            "extremeIntMax" => ["field", PHP_INT_MAX, false,],
            "typicalStringable" => ["field", new class
            {
                public function __toString(): string
                {
                    return "string";
                }
            }, false,],
            "typicalObject" => ["field", (object)[], false,],
            "typicalAnonymousClass" => ["field", new class{}, false,],
            "typicalInt" => ["field", 123, false,],
            "typicalFloat" => ["field", 123.456, false,],
            "typicalNull" => ["field", null, false,],
            "typicalArray" => ["field", ["foo",], false,],
            "extremeEmptyArray" => ["field", [], false,],
            "extremeStringableTrue" => ["field", new class
            {
                public function __toString(): string
                {
                    return "true";
                }
            }, false,],
            "extremeStringableInt1" => ["field", new class
            {
                public function __toString(): string
                {
                    return "1";
                }
            }, false,],
            "extremeStringableYes" => ["field", new class
            {
                public function __toString(): string
                {
                    return "yes";
                }
            }, false,],
            "extremeStringableOn" => ["field", new class
            {
                public function __toString(): string
                {
                    return "on";
                }
            }, false,],
            "extremeStringableFalse" => ["field", new class
            {
                public function __toString(): string
                {
                    return "false";
                }
            }, false,],
            "extremeStringableInt0" => ["field", new class
            {
                public function __toString(): string
                {
                    return "0";
                }
            }, false,],
            "extremeStringableNo" => ["field", new class
            {
                public function __toString(): string
                {
                    return "no";
                }
            }, false,],
            "extremeStringableOff" => ["field", new class
            {
                public function __toString(): string
                {
                    return "off";
                }
            }, false,],
            "extremeArrayStringTrue" => ["field", ["true",], false,],
            "extremeArrayStringFalse" => ["field", ["false",], false,],
            "extremeArrayTrue" => ["field", [true,], false,],
            "extremeArrayFalse" => ["field", [false,], false,],
            "extremeArrayInt1" => ["field", [1,], false,],
            "extremeArrayInt0" => ["field", [0,], false,],
            "extremeArrayStringOn" => ["field", ["on",], false,],
            "extremeArrayStringOff" => ["field", ["off",], false,],
            "extremeArrayStringYes" => ["field", ["yes",], false,],
            "extremeArrayStringNo" => ["field", ["no",], false,],

            "invalidIntField" => [1, "", false, TypeError::class,],
            "invalidFloatField" => [1.5, "", false, TypeError::class,],
            "invalidNullField" => [null, "", false, TypeError::class,],
            "invalidEmptyArrayField" => [[], "", false, TypeError::class,],
            "invalidStringableField" => [new class
            {
                public function __toString(): string
                {
                    return "field";
                }
            }, "", false, TypeError::class,],
            "invalidArrayField" => [["field",], "", false, TypeError::class,],
            "invalidTrueField" => [true, "", false, TypeError::class,],
            "invalidFalseField" => [false, "", false, TypeError::class,],
        ];
    }

    /**
     * Test the passes() method.
     *
     * @dataProvider dataForTestPasses
     */
    public function testPasses($field, $data, bool $shouldPass, ?string $exceptionClass = null): void
    {
        if (isset($exceptionClass)) {
            $this->expectException($exceptionClass);
        }

        $rule = $this->ruleInstance();
        $this->assertSame($shouldPass, $rule->passes($field, $data), "The rule did not provide the expected result from passes().");
    }
}
