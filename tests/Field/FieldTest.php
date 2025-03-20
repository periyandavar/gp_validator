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
        $field->addMessage('This field is required');

        $this->assertEquals(['This field is required'], $field->getMessages());
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

        $this->assertEquals(['Invalid value'], $field->getErrors());
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
