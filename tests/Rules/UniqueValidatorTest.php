<?php

use PHPUnit\Framework\TestCase;
use Validator\Field\Field;
use Validator\Rules\UniqueValidator;
use Validator\ValidationConstants;

class UniqueValidatorTest extends TestCase
{
    private $mockDb;

    protected function setUp(): void
    {
        // Create a mock for the database object
        $this->mockDb = Mockery::mock(DB::class);
    }

    public function testValidateUniqueValue()
    {
        $field = new Field('test', 'uniqueValue');

        // Mock the database handler method to simulate a unique value scenario
        $this->mockDb
            ->shouldReceive('checkUnique')
            ->andReturn(true);

        $validator = new UniqueValidator($this->mockDb, 'checkUnique', ['table' => 'users', 'column' => 'email']);
        $result = $validator->validate($field);

        $this->assertTrue($result, 'Expected validation to pass for a unique value');
        $this->assertEmpty($validator->getError(), 'Expected no error message for a unique value');
    }

    public function testValidateNonUniqueValue()
    {
        $field = new Field('test', 'nonUniqueValue');

        // Mock the database handler method to simulate a unique value scenario
        $this->mockDb
            ->shouldReceive('checkUnique')
            ->andReturn(false);

        $validator = new UniqueValidator($this->mockDb, 'checkUnique', ['table' => 'users', 'column' => 'email']);
        $result = $validator->validate($field);

        $this->assertFalse($result, 'Expected validation to fail for a non-unique value');
        $this->assertEquals(
            'The value should be unique',
            $validator->getError(),
            'Expected error message for a non-unique value'
        );
    }

    public function testGetName()
    {
        $this->assertEquals(
            ValidationConstants::UNIQUE_VALIEATOR,
            UniqueValidator::getName(),
            'Expected the validator name to match the constant'
        );
    }
}

class DB
{
}
