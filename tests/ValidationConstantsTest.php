<?php

use Validator\ValidationConstants;

class ValidationConstantsTest extends \PHPUnit\Framework\TestCase
{
    public function testGetValidationObj()
    {
        $this->assertNull(ValidationConstants::getValidationObj('nonExistentRule'));
    }

    public function testGetDerivedRule()
    {
        $this->assertNull(ValidationConstants::getDerivedRule('nonExistentRule'));
    }
}
