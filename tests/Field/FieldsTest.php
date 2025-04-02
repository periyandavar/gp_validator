<?php

namespace Validator\Tests;

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Field\Fields;
use Validator\Rules\ValidationRule;

class FieldsTest extends TestCase
{
    public function testAddFields()
    {
        $field1 = $this->createMock(Field::class);
        $field1->method('getName')->willReturn('field1');

        $field2 = $this->createMock(Field::class);
        $field2->method('getName')->willReturn('field2');

        $fields = new Fields();
        $fields->addFields($field1, $field2);

        $this->assertCount(2, $fields->getValues());
    }

    public function testGetValues()
    {
        $field1 = $this->createMock(Field::class);
        $field1->method('getName')->willReturn('field1');
        $field1->method('getData')->willReturn('value1');

        $field2 = $this->createMock(Field::class);
        $field2->method('getName')->willReturn('field2');
        $field2->method('getData')->willReturn('value2');

        $fields = new Fields([$field1, $field2]);

        $values = $fields->getValues();
        $this->assertEquals(['field1' => 'value1', 'field2' => 'value2'], $values);
    }

    public function testValidateFields()
    {
        $field1 = $this->createMock(Field::class);
        $field1->method('validate')->willReturn(true);

        $field2 = $this->createMock(Field::class);
        $field2->method('validate')->willReturn(false);

        $fields = new Fields([$field1, $field2]);

        $result = $fields->validate();
        $this->assertFalse($result);
    }

    public function testAddField()
    {
        $field = new Field('testField');
        $fields = new Fields();

        $fields->addField($field);

        $this->assertCount(1, iterator_to_array($fields));
        $this->assertArrayHasKey('testField', iterator_to_array($fields));
    }

    public function testRemoveFields()
    {
        $field1 = new Field('testField1');
        $field2 = new Field('testField2');
        $fields = new Fields([$field1, $field2]);

        $fields->removeFields('testField1');

        $this->assertCount(1, iterator_to_array($fields));
        $this->assertArrayNotHasKey('testField1', iterator_to_array($fields));
    }

    public function testAddValues()
    {
        $field1 = new Field('testField1');
        $field2 = new Field('testField2');
        $fields = new Fields([$field1, $field2]);

        $fields->addValues(['testField1' => 'value1', 'testField2' => 'value2']);

        $this->assertEquals('value1', $field1->getData());
        $this->assertEquals('value2', $field2->getData());
    }

    public function testAddRule()
    {
        $field1 = new Field('testField1');
        $field2 = new Field('testField2');
        $fields = new Fields([$field1, $field2]);

        $fields->addRule(['testField1' => 'rule1', 'testField2' => ['rule2', 'rule3']]);

        $this->assertContains('rule1', $field1->getRules());
        $this->assertContains('rule2', $field2->getRules());
        $this->assertContains('rule3', $field2->getRules());
    }

    public function testSetRequiredFields()
    {
        $field1 = new Field('testField1');
        $field2 = new Field('testField2');
        $fields = new Fields([$field1, $field2]);

        $fields->setRequiredFields('testField1', 'testField2');

        $this->assertContains('required', $field1->getRules());
        $this->assertContains('required', $field2->getRules());
    }

    public function testRenameFieldName()
    {
        $field = new Field('oldName');
        $fields = new Fields([$field]);

        $fields->renameFieldName('oldName', 'newName');

        $this->assertArrayHasKey('newName', iterator_to_array($fields));
        $this->assertArrayNotHasKey('oldName', iterator_to_array($fields));
    }

    public function testGetData()
    {
        $field1 = new Field('testField1');
        $field2 = new Field('testField2');
        $field1->setData('value1');
        $field2->setData('value2');
        $fields = new Fields([$field1, $field2]);

        $data = $fields->getData();

        $this->assertSame(['testField1' => 'value1', 'testField2' => 'value2'], $data);
    }

    public function testAddCustomRule()
    {
        $field = new Field('testField');
        $fields = new Fields([$field]);
        $customRule = $this->createMock(ValidationRule::class);

        $fields->addCustomeRule('testField', $customRule);

        $this->assertContains($customRule, $field->getRules());
    }

    public function testSetData()
    {
        $field = new Field('testField');
        $fields = new Fields([$field]);

        $fields->setData('testField', 'newValue');

        $this->assertSame('newValue', $field->getData());
    }

    public function testValidate()
    {
        $field1 = $this->createMock(Field::class);
        $field2 = $this->createMock(Field::class);

        $field1->method('validate')->willReturn(true);
        $field2->method('validate')->willReturn(false);

        $fields = new Fields([$field1, $field2]);

        $this->assertFalse($fields->validate());
    }
}
