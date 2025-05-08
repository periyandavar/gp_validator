<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\LengthValidator;
use Validator\ValidationConstants;

class LengthValidatorTest extends TestCase
{
    public function testValidateExactLength()
    {
        $field = new Field('test', '12345');

        $validator = new LengthValidator(null, null, 5);

        $result = $validator->validate($field);
        $this->assertTrue($result, 'Expected validation to pass for exact length');

        $field->setData('1234');
        $result = $validator->validate($field);
        $this->assertFalse($result, 'Expected validation to fail for incorrect exact length');
    }

    public function testValidateMinLength()
    {
        $field = new Field('test', '12345');

        $validator = new LengthValidator(3);

        $result = $validator->validate($field);
        $this->assertTrue($result, 'Expected validation to pass for length greater than min');

        $field->setData('12');
        $result = $validator->validate($field);
        $this->assertFalse($result, 'Expected validation to fail for length less than min');
    }

    public function testValidateMaxLength()
    {
        $field = new Field('test', '123');

        $validator = new LengthValidator(null, 6);

        $result = $validator->validate($field);
        $this->assertTrue($result, 'Expected validation to pass for length less than max');

        $field->setData('1234567');
        $result = $validator->validate($field);
        $this->assertFalse($result, 'Expected validation to fail for length greater than max');
    }

    public function testValidateBetweenLength()
    {
        $field = new Field('tes');
        $field->setData('5212');

        $validator = new LengthValidator(3, 6);

        $result = $validator->validate($field);
        $this->assertTrue($result, 'Expected validation to pass for length between min and max');

        $field->setData('12');
        $result = $validator->validate($field);
        $this->assertFalse($result, 'Expected validation to fail for length less than min');

        $field->setData('1234567');
        $result = $validator->validate($field);
        $this->assertFalse($result, 'Expected validation to fail for length greater than max');
    }

    public function testGetName()
    {
        $this->assertEquals(ValidationConstants::LENGTH_VALIDATOR, LengthValidator::getName(), 'Expected the validator name to match');
    }
}
