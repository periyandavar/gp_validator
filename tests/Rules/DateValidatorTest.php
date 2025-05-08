<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\DateValidator;
use Validator\ValidationConstants;

class DateValidatorTest extends TestCase
{
    public function testValidationSuccess()
    {
        // Test cases for valid dates
        $testCases = [
            ['format' => 'Y-m-d', 'value' => '2023-10-01', 'expected' => true],
            ['format' => 'd/m/Y', 'value' => '01/10/2023', 'expected' => true],
            ['format' => 'm-d-Y', 'value' => '10-01-2023', 'expected' => true],
            ['format' => 'Y-m-d H:i:s', 'value' => '2023-10-01 14:30:00', 'expected' => true],
        ];

        foreach ($testCases as $testCase) {
            $field = $this->createMock(Field::class);
            $field->method('getData')->willReturn($testCase['value']);

            $validator = new DateValidator($testCase['format']);

            $result = $validator->validate($field);
            $this->assertSame($testCase['expected'], $result, "Failed for format: {$testCase['format']} with value: {$testCase['value']}");
        }
    }

    public function testValidationFailure()
    {
        // Test cases for invalid dates
        $testCases = [
            ['format' => 'Y-m-d', 'value' => '01-10-2023', 'expectedMessage' => 'The value should match the date format Y-m-d'],
            ['format' => 'd/m/Y', 'value' => '2023/10/01', 'expectedMessage' => 'The value should match the date format d/m/Y'],
            ['format' => 'm-d-Y', 'value' => '2023-10-01', 'expectedMessage' => 'The value should match the date format m-d-Y'],
            ['format' => 'Y-m-d H:i:s', 'value' => '2023-10-01T14:30:00', 'expectedMessage' => 'The value should match the date format Y-m-d H:i:s'],
        ];

        foreach ($testCases as $testCase) {
            $field = $this->createMock(Field::class);
            $field->method('getData')->willReturn($testCase['value']);
            $field->method('getName')->willReturn('TestField');

            $validator = new DateValidator($testCase['format']);
            $validator->setMessage($testCase['expectedMessage'], 'format');

            $result = $validator->validate($field);
            $this->assertFalse($result, "Validation passed unexpectedly for format: {$testCase['format']} with value: {$testCase['value']}");

            // Assert error message
            $this->assertEquals($testCase['expectedMessage'], $validator->getError());
        }
    }

    public function testGetName()
    {
        // Assert the static method getName returns the correct name
        $this->assertEquals(ValidationConstants::DATE_VALIDATOR, DateValidator::getName());
    }

    public function testValidationWithMinMaxDates()
    {
        $field = new Field('dateField', '2023-10-15');

        $validator = new DateValidator('Y-m-d', '2023-10-10', '2023-10-20');
        $this->assertTrue($validator->validate($field), 'Expected validation to pass for date within range');

        $field->setData('2023-10-09');
        $this->assertFalse($validator->validate($field), 'Expected validation to fail for date earlier than minDate');
        $this->assertEquals(
            'The value should be between 2023-10-10 and 2023-10-20',
            $validator->getError()
        );

        $validator = new DateValidator('Y-m-d', null, '2023-10-20');
        $validator->setMessage('The date should not be later than {max}', 'max');

        $field->setData('2023-10-21');
        $this->assertFalse($validator->validate($field), 'Expected validation to fail for date later than maxDate');
        $this->assertEquals(
            'The date should not be later than 2023-10-20',
            $validator->getError()
        );

        $validator = new DateValidator('Y-m-d', '2023-10-20');
        $validator->setMessage('The date should not be earlier than {min}', 'min');

        $field->setData('2023-10-01');
        $this->assertFalse($validator->validate($field), 'Expected validation to fail for date later than maxDate');
        $this->assertEquals(
            'The date should not be earlier than 2023-10-20',
            $validator->getError()
        );
    }
}
