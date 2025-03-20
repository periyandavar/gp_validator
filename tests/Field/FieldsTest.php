<?php

namespace Validator\Tests;

use PHPUnit\Framework\TestCase;
use Validator\Field\Fields;
use Validator\Field\Field;

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
}