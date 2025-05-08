<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\ValidationEngine;

class FieldTest extends TestCase
{
    public function testConstructorInitializesProperties()
    {
        $field = new Field('testField', 'testData', ['required']);

        $this->assertEquals('testField', $field->getName());
        $this->assertEquals('testData', $field->getData());
        $this->assertEquals(['required'], $field->getRules());
    }

    public function testGetNameAndSetName()
    {
        $field = new Field('testField');
        $field->setName('newName');

        $this->assertEquals('newName', $field->getName());
    }

    public function testGetDataAndSetData()
    {
        $field = new Field('testField');
        $field->setData('newData');

        $this->assertEquals('newData', $field->getData());
    }

    public function testGetRulesAndSetRules()
    {
        $field = new Field('testField');
        $field->setRules(['required', 'email']);

        $this->assertEquals(['required', 'email'], $field->getRules());
    }

    public function testAddRule()
    {
        $field = new Field('testField');
        $field->addRule('required');

        $this->assertEquals(['required'], $field->getRules());
    }

    public function testAddRules()
    {
        $field = new Field('testField');
        $field->addRules(['required', 'email']);

        $this->assertEquals(['required', 'email'], $field->getRules());
    }

    public function testGetMessagesAndSetMessages()
    {
        $field = new Field('testField');
        $field->setMessages(['required' => 'This field is required']);

        $this->assertEquals(['required' => 'This field is required'], $field->getMessages());
    }

    public function testAddMessage()
    {
        $field = new Field('testField');
        $field->addMessage('required', 'This field is required');

        $this->assertEquals(['required' => 'This field is required'], $field->getMessages());
    }

    public function testIsValidAndSetValid()
    {
        $field = new Field('testField');
        $field->setValid(true);

        $this->assertTrue($field->isValid());
    }

    public function testGetErrorsAndAddError()
    {
        $field = new Field('testField');
        $field->addError('Invalid value');
        $field->addWarning('Warning value');

        $this->assertEquals(['Invalid value'], $field->getErrors());
        $this->assertEquals('Invalid value', $field->getError());
        $field->setErrors([]);
        $this->assertEquals($field->getWarning(), 'Warning value');
        $this->assertEmpty($field->getErrors());
    }

    public function testAddRuleMessage()
    {
        $field = new Field('testField');
        $msg = 'This field must be numeric and less than {max}';
        $field1 = new Field('testField2');
        $field3 = new Field('testField3');
        $field3->addMessage('numeric', 'Invalid Number');
        $field->addRuleMessage('numeric', 'max', $msg);
        $this->assertEquals($msg, $field->getRuleMessage('numeric', 'max'));
        $this->assertEquals($msg, $field->getRuleMessage('numeric'));
        $this->assertNull($field1->getRuleMessage('numeric', 'max'));
        $this->assertEquals('Invalid Number', $field3->getRuleMessage('numeric', 'max'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testValidateCallsValidationEngine()
    {
        $mockValidationEngine = Mockery::mock('overload:' . ValidationEngine::class);
        $mockValidationEngine->shouldReceive('validateField')->andReturn(true)->once();

        $field = new Field('testField');
        $this->assertTrue($field->validate());
    }
}
