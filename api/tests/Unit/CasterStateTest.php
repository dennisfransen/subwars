<?php

namespace Tests\Unit;

use App\Http\Enums\CasterState;
use Tests\TestCase;

class CasterStateTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = new CasterState();
    }

    public function testItGetsStringOfInteger()
    {
        $this->assertEquals("MAIN_CASTER", $this->record->getStringOfInteger(CasterState::MAIN_CASTER));
        $this->assertEquals("CO_CASTER", $this->record->getStringOfInteger(CasterState::CO_CASTER));
        $this->assertEquals("OWNER", $this->record->getStringOfInteger(CasterState::OWNER));
        $this->assertEquals("UNDEFINED", $this->record->getStringOfInteger(-1));
    }

    public function testItGetsIntegerOfString()
    {
        $this->assertEquals(CasterState::MAIN_CASTER, $this->record->getIntegerOfString("MAIN_CASTER"));
        $this->assertEquals(CasterState::CO_CASTER, $this->record->getIntegerOfString("CO_CASTER"));
        $this->assertEquals(CasterState::OWNER, $this->record->getIntegerOfString("OWNER"));
    }

    public function testItGetsFullIntegerArray()
    {
        $expectedArray = [CasterState::MAIN_CASTER, CasterState::CO_CASTER, CasterState::OWNER];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray(["MAIN_CASTER", "CO_CASTER", "OWNER"]));
    }

    public function testItGetsPartlyIntegerArray()
    {
        $expectedArray = [CasterState::MAIN_CASTER, CasterState::OWNER];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray(["MAIN_CASTER", "OWNER"]));
    }

    public function testItGetsStringArray()
    {
        $expectedArray = [
            "MAIN_CASTER",
            "CO_CASTER",
            "OWNER",
        ];

        $this->assertEquals($expectedArray, $this->record->getStringArray());
    }
}
