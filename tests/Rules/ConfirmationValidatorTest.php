<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\ConfirmationValidator;
use Validator\ValidationConstants;

class ConfirmationValidatorTest extends TestCase
{
    public function testValidationSuccess()
    {
        // Mock field that will be used for comparison
        $field1 = $this->createMock(Field::class);
        $field1->method('getData')->willReturn('testValue');
        $field1->method('getName')->willReturn('TestField');

        // Field to be validated
        $field2 = $this->createMock(Field::class);
        $field2->method('getData')->willReturn('testValue');

        $validator = new ConfirmationValidator($field1);

        // Assert validation passes
        $this->assertTrue($validator->validate($field2));
    }

    public function testValidationFailure()
    {
        // Mock field that will be used for comparison
        $field1 = $this->createMock(Field::class);
        $field1->method('getData')->willReturn('testValue');
        $field1->method('getName')->willReturn('TestField');

        // Field to be validated
        $field2 = $this->createMock(Field::class);
        $field2->method('getData')->willReturn('differentValue');

        $validator = new ConfirmationValidator($field1);

        // Assert validation fails
        $this->assertFalse($validator->validate($field2));

        // Assert error message is set
        $expectedErrorMessage = 'The value should be equal to TestField';
        $this->assertEquals($expectedErrorMessage, $validator->getErrorMessage());
    }

    public function testGetName()
    {
        // Assert the static method getName returns the correct name
        $this->assertEquals(ValidationConstants::CONFIRMATION_VALIDATIOR, ConfirmationValidator::getName());
    }
}
