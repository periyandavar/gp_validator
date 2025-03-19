<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\ValidationEngine;

class ValidationEngineTest extends TestCase
{
    private $validationEngine;

    protected function setUp(): void
    {
        $this->validationEngine = new ValidationEngine();
    }

    public function testMobileNumberValidation()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn('9876543210');
        $field->method('getName')->willReturn('mobile');
        $field->expects($this->once())->method('setValid')->with(true);

        $result = $this->validationEngine->mobileNumberValidation($field);
        $this->assertTrue($result);
    }

    public function testEmailValidation()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn('test@example.com');
        $field->method('getName')->willReturn('email');
        $field->expects($this->once())->method('setValid')->with(true);

        $result = $this->validationEngine->emailValidation($field);
        $this->assertTrue($result);
    }

    public function testNumericValidationWithinRange()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn(50);
        $field->method('getName')->willReturn('age');
        $field->expects($this->once())->method('setValid')->with(true);

        $result = $this->validationEngine->numericValidation($field, 10, 100);
        $this->assertTrue($result);
    }

    public function testNumericValidationOutOfRange()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn(5);
        $field->method('getName')->willReturn('age');
        $field->expects($this->once())->method('setValid')->with(false);

        $result = $this->validationEngine->numericValidation($field, 10, 100);
        $this->assertFalse($result);
    }

    public function testRequiredFieldValidation()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn('value');
        $field->method('getName')->willReturn('requiredField');
        $field->expects($this->once())->method('setValid')->with(true);

        $result = $this->validationEngine->required($field);
        $this->assertTrue($result);
    }

    public function testValidateMultipleFields()
    {
        // $field1 = $this->createMock(Field::class);
        // $field1->method('getData')->willReturn('9876543210');
        // $field1->method('getName')->willReturn('mobile');
        // $field1->method('getRules')->willReturn(['mobileNumberValidation']);
        // $field1->expects($this->once())->method('setValid')->with(true);

        // $field2 = $this->createMock(Field::class);
        // $field2->method('getData')->willReturn('test@example.com');
        // $field2->method('getName')->willReturn('email');
        // $field2->method('getRules')->willReturn(['emailValidation']);
        // $field2->expects($this->once())->method('setValid')->with(true);

        $field = new Field('mobile', '9876543210', ['mobileNumberValidation']);
        $field2 = new Field('email', 'viky', ['emailValidation']);
        $fields = new Fields();
        $fields->addFields($field, $field2);
        // var_export($fields);
        $invalidFields = [];
        $result = $this->validationEngine->validate($fields, false, $invalidFields);
        $this->assertEquals($invalidFields, [['email' => ['email should be a valid email id']]]);
        $this->assertFalse($result);
    }
}
