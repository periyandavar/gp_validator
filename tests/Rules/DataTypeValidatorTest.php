<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\DataTypeValidator;
use Validator\ValidationConstants;

class DataTypeValidatorTest extends TestCase
{
    public function testValidationSuccess()
    {
        // Test cases for successful validation
        $testCases = [
            ['type' => 'string', 'value' => 'testString', 'expected' => true],
            ['type' => 'integer', 'value' => 123, 'expected' => true],
            ['type' => 'float', 'value' => 12.34, 'expected' => true],
            ['type' => 'boolean', 'value' => true, 'expected' => true],
            ['type' => 'array', 'value' => ['key' => 'value'], 'expected' => true],
            ['type' => 'null', 'value' => null, 'expected' => true],
            ['type' => 'nulls', 'value' => null, 'expected' => false],
        ];

        foreach ($testCases as $testCase) {
            $field = $this->createMock(Field::class);
            $field->method('getData')->willReturn($testCase['value']);

            $validator = new DataTypeValidator($testCase['type']);

            $result = $validator->validate($field);
            $this->assertSame($testCase['expected'], $result, "Failed for type: {$testCase['type']}");
        }
    }

    public function testValidationFailure()
    {
        // Test cases for failed validation
        $testCases = [
            ['type' => 'string', 'value' => 123, 'expectedMessage' => 'The value should be a string'],
            ['type' => 'integer', 'value' => 'testString', 'expectedMessage' => 'The value should be an integer'],
            ['type' => 'float', 'value' => true, 'expectedMessage' => 'The value should be a float'],
            ['type' => 'boolean', 'value' => 'false', 'expectedMessage' => 'The value should be a boolean'],
            ['type' => 'array', 'value' => 'notAnArray', 'expectedMessage' => 'The value should be an array'],
            ['type' => 'null', 'value' => 'notNull', 'expectedMessage' => 'The value should be null'],
        ];

        foreach ($testCases as $testCase) {
            $field = $this->createMock(Field::class);
            $field->method('getData')->willReturn($testCase['value']);
            $field->method('getName')->willReturn('TestField');

            $validator = new DataTypeValidator($testCase['type']);

            $result = $validator->validate($field);
            $this->assertFalse($result, "Validation passed unexpectedly for type: {$testCase['type']}");

            // Assert error message
            $this->assertEquals($testCase['expectedMessage'], $validator->getDefaultMessage());
        }
    }

    public function testGetName()
    {
        // Assert the static method getName returns the correct name
        $this->assertEquals(ValidationConstants::DATA_TYPE_VALIDATOR, DataTypeValidator::getName());
    }
}
