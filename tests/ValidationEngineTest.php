<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\Rules\DataTypeValidator;
use Validator\Rules\LengthValidator;
use Validator\Rules\NumericValidator;
use Validator\Rules\ValidationRule;
use Validator\ValidationConstants;
use Validator\ValidationEngine;

class ValidationEngineTest extends TestCase
{
    private $validationEngine;

    protected function setUp(): void
    {
        $this->validationEngine = new ValidationEngine();
    }

    public function testIsbnValidation()
    {
        $field = $this->createMock(Field::class);

        $field->expects($this->any())
            ->method('getData')
            ->willReturn('123-4-56-789012-3'); // Invalid ISBN
            ->method('getData')
            ->willReturn('123-4-56-789012-3'); // Invalid ISBN

        $result = $this->validationEngine->isbnValidation($field);
        $this->assertFalse($result);
    }

    public function testLengthValidation()
    {
        $field = $this->createMock(Field::class);
        $field->expects($this->any())
            ->method('getData')
            ->willReturn('12345');
            ->method('getData')
            ->willReturn('12345');

        // Test valid length
        $field->expects($this->any())
            ->method('getRules')
            ->willReturn(['length', [5]]);
            ->method('getRules')
            ->willReturn(['length', [5]]);

        $lengthValidation = new LengthValidator();
        $result = $lengthValidation->validate($field);
        $this->assertTrue($result);

        // Test invalid length
        $field->expects($this->any())
            ->method('getRules')
            ->willReturn(['length', ['10']]);
            ->method('getRules')
            ->willReturn(['length', ['10']]);

        $lengthValidation = new LengthValidator(10);

        $result = $lengthValidation->validate($field);
        $this->assertFalse($result);
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
        $numericValidation = new NumericValidator(10, 100);
        $result = $numericValidation->validate($field);
        $this->assertTrue($result);
    }

    public function testNumericValidationOutOfRange()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn(5);
        $field->method('getName')->willReturn('age');
        $numericValidation = new NumericValidator(10, 100);
        $result = $numericValidation->validate($field);
        $this->assertFalse($result);
    }

    public function testNumericValidationWithStr()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn('yyy');
        $field->method('getName')->willReturn('age');
        $numericValidation = new NumericValidator(10, 100);
        $result = $numericValidation->validate($field);
        $this->assertFalse($result);
    }

    public function testRequiredFieldValidation()
    {
        $field = $this->createMock(Field::class);
        $field->method('getData')->willReturn('value');
        $field->method('getName')->willReturn('requiredField');
        $field->expects($this->once())->method('setValid')->with(true);

        $result = $this->validationEngine->requiredValidation($field);
        $this->assertTrue($result);
    }

    public function testValidateMultipleFields()
    {
        $field = new Field('mobile', '9876543210', ['mobileNumberValidation']);
        $field2 = new Field('email', 'viky', ['emailValidation']);
        $fields = new Fields();
        $fields->addFields($field, $field2);
        $invalidFields = [];
        $result = $this->validationEngine->validate($fields, false, $invalidFields);
        $this->assertEquals($invalidFields, [['email' => ['email should be a valid email id']]]);
        $this->assertFalse($result);
    }

    public function testValidateMultipleValidFields()
    {
        $field1 = new Field('mobile', '9876543210', ['mobileNumberValidation']);
        $field2 = new Field('email', 'vicky@gmail.com', ['emailValidation', 'required', [CValidation::class, true], ['length', ['min' => 10, 'max' => 20]]]);
        $field3 = new Field('age', 50, [[ValidationConstants::NUMERIC_VALIDATOR, ['min' => 10, 'max' => 100]], ['positiveNumber']]);
        $field4 = new Field('name', 'vicky', ['alphaspace']);
        $field5 = new Field('value', '71', [['regex', '/\d+/'], ['valuesIn', [10,10, 37, 71]]]);
        $field6 = new Field('isbn', '0-19-852663-6', ['isbn']);
        $field7 = new Field('landline', '12345 123456', 'landline');
        // $field8 = new Field('v5', '030640615X', ['isbn10']);
        $fields = new Fields();
        $fields->addFields($field1, $field2, $field3, $field4, $field5, $field6, $field7, 'dummy');
        $fields->addRule([
            'dummy' => ['alpha'],
        ]);
        $fields->setValues(
            ['dummy' => 'wer']
        );
        $invalidFields = [];
        $result = $this->validationEngine->validate($fields, false, $invalidFields);
        $this->assertEmpty($invalidFields);
        $this->assertTrue($result);
    }

    public function testValidateMultipleInValidFields()
    {
        $field1 = new Field('mobile', '987210', ['mobileNumberValidation']);
        $field2 = new Field('email', 'vickygmail.com', ['emailValidation', 'required', [CValidation::class, true], ['length', ['min' => 10, 'max' => 15]]]);
        $field3 = new Field('age', 500, [['numericValidation', [10, 100]], ['positiveNumber']], ['numericValidation' => ['max' => 'It should be between {10} and {100}']]);
        $field4 = new Field('name', 'vick%y', ['alphaNumeric', [CValidation::class, ['valid' => true]]]);
        $field5 = new Field('value', '719', [['regex', '/\d+/'], ['valuesIn', [10,10, 37, 71]]]);
        $field6 = new Field('isbn', '0306//4061152', ['isbn13']);
        $field7 = new Field('landline', '1234518kk23456', 'landline', ['landline' => 'Please enter valid landline number']);
        $field8 = new Field('url', 'https://example.com', [['url'], ['valuesNotIn', ['https://example.com']]]);
        $fields = new Fields();
        $field9 = new Field('v1', '0306406X52123', ['isbn13']);
        $field10 = new Field('v2', '030X6151X', ['isbn10', 'length 7']);
        $field11 = new Field('v3', '0306406151X', [new LengthValidator(10)]);
        $field12 = new Field('v4', '03011111111116151X', ['isbn', 'length 7']);
        $field13 = new Field('v5', '030640615X', ['isbn10']);
        $field14 = new Field('v6', '030640X15X', ['isbn10']);

        $fields->addFields($field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8);
        $fields->addFields($field10, $field9, $field11, 'dummy', $field12, $field13, $field14);
        $invalidFields = [];
        $result = $this->validationEngine->validate($fields, false, $invalidFields);
        $this->assertEquals('Please enter valid landline number', $field7->getError());
        $this->assertNotEmpty($invalidFields);
        $this->assertFalse($result);
    }

    public function testValidateMultipleInValidFieldsPassOnFail()
    {
        $field1 = new Field('mobile', '987210', ['mobileNumber', 'length 10'], ['mobileNumber' => 'Please enter valid mobile number']);
        $field2 = new Field('email', 'vickygmail.com', ['emailValidation', 'required', [CValidation::class, true], ['length', ['min' => 10, 'max' => 15]]]);
        $field3 = new Field('age', 500, [['numericValidation', [10, 100]], ['positiveNumber']], ['numericValidation' => ['max' => 'It should be between {10} and {100}']]);
        $field4 = new Field('name', 'vick%y', ['alphaNumeric', [CValidation::class, ['valid' => true]]]);
        $field5 = new Field('value', '719', [['regex', '/\d+/'], ['valuesIn', [10,10, 37, 71]]]);
        $fields = new Fields();
        $fields->addFields($field1, $field2, $field3, $field4, $field5);
        $invalidFields = [];
        $result = $this->validationEngine->validate($fields, true, $invalidFields);
        $this->assertCount(1, $invalidFields);
        $this->assertFalse($result);
    }

    public function testDerivedValidator()
    {
        $field = new Field('testField', 'testValue', [ValidationConstants::INTEGER_VALIDATOR]);
        $fields = new Fields([$field]);
        $validationEngine = new ValidationEngine();
        $invalidFields = [];
        $validationEngine->validate($fields, false, $invalidFields);
        $this->assertCount(1, $invalidFields);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testExecuteRule()
    {
        $field = new Field('testField', 'testValue');
        $field->setValid(false);
        $validationEngine = new ValidationEngine();
        $dtValidator = Mockery::mock('overload:' . DataTypeValidator::class);
        $dtValidator->shouldNotReceive('validate');

        $this->assertFalse($validationEngine->executeRule($field, ValidationConstants::INTEGER_VALIDATOR));
    }

    public function testInvalidRule()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid rule: invalidRule');
        $field = new Field('testField', 'testValue', ['invalidRule']);
        $fields = new Fields([$field]);
        $validationEngine = new ValidationEngine();
        $validationEngine->validate($fields, false);
    }
}

class CValidation implements ValidationRule
{
    private $valid;

    public function validate(Field $field): bool
    {
        return (bool) $this->valid;
    }

    public function __construct($valid)
    {
        $this->valid = $valid;
    }

    public static function getName(): string
    {
        return 'CValidation';
    }
}
