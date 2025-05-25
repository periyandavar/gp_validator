<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\NumericValidator;
use Validator\ValidationConstants;

class NumericValidatorTest extends TestCase
{
    public function testValidateNumeric()
    {
        $field = new Field('123', 123);
        $validator = new NumericValidator();
        $result = $validator->validate($field);

        $this->assertTrue($result, 'Expected validation to pass for numeric value');
        $this->assertEmpty($validator->getError(), 'Expected no error message');

        $field->setData('abc');
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for non-numeric value');
        $this->assertEquals('The value should be a numeric value', $validator->getError(), 'Expected default error message for non-numeric value');
    }

    public function testValidateMin()
    {
        $field = new Field('TestField', 10);

        $validator = new NumericValidator(5);
        $result = $validator->validate($field);

        $this->assertTrue($result, 'Expected validation to pass for value greater than min');

        $field->setData(3);
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for value less than min');
        $this->assertEquals('The value should be greater than or equal to 5', $validator->getError(), 'Expected min error message');
    }

    public function testValidateMax()
    {
        $field = new Field('TestField', 20);

        $validator = new NumericValidator(null, 30);
        $result = $validator->validate($field);

        $this->assertTrue($result, 'Expected validation to pass for value less than max');

        $field->setData(40);
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for value greater than max');
        $this->assertEquals('The value should be less than or equal to 30', $validator->getError(), 'Expected max error message');
    }

    public function testValidateRange()
    {
        $field = new Field('TestField', 15);

        $validator = new NumericValidator(10, 20);
        $result = $validator->validate($field);

        $this->assertTrue($result, 'Expected validation to pass for value within range');

        $field->setData(25);
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for value greater than max in range');
        $this->assertEquals('The value should be between 10 and 20', $validator->getError(), 'Expected range error message');

        $field->setData(5);
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for value less than min in range');
        $this->assertEquals('The value should be between 10 and 20', $validator->getError(), 'Expected range error message');
    }

    public function testGetName()
    {
        $this->assertEquals(ValidationConstants::NUMERIC_VALIDATOR, NumericValidator::getName(), 'Expected the validator name to match');
    }
}
