<?php

namespace Tests\Unit;

use App\Http\Enums\TournamentUserState;
use Tests\TestCase;

class TournamentUserStateTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = new TournamentUserState();
    }

    public function testItGetsStringOfInteger()
    {
        $this->assertEquals("CHECKED_IN", $this->record->getStringOfInteger(TournamentUserState::CHECKED_IN));
        $this->assertEquals("REGISTERED", $this->record->getStringOfInteger(TournamentUserState::REGISTERED));
        $this->assertEquals("REGISTERED_KICKED", $this->record->getStringOfInteger(TournamentUserState::REGISTERED_KICKED));
        $this->assertEquals("CHECKED_IN_KICKED", $this->record->getStringOfInteger(TournamentUserState::CHECKED_IN_KICKED));
        $this->assertEquals("UNDEFINED", $this->record->getStringOfInteger(-1));
    }

    public function testItGetsIntegerOfString()
    {
        $this->assertEquals(TournamentUserState::CHECKED_IN, $this->record->getIntegerOfString("CHECKED_IN"));
        $this->assertEquals(TournamentUserState::REGISTERED, $this->record->getIntegerOfString("REGISTERED"));
        $this->assertEquals(TournamentUserState::REGISTERED_KICKED, $this->record->getIntegerOfString("REGISTERED_KICKED"));
        $this->assertEquals(TournamentUserState::CHECKED_IN_KICKED, $this->record->getIntegerOfString("CHECKED_IN_KICKED"));
    }

    public function testItGetsFullIntegerArray()
    {
        $expectedArray = [
            TournamentUserState::CHECKED_IN,
            TournamentUserState::REGISTERED,
            TournamentUserState::REGISTERED_KICKED,
            TournamentUserState::CHECKED_IN_KICKED,
        ];
        $inputArray = [
            "CHECKED_IN",
            "REGISTERED",
            "REGISTERED_KICKED",
            "CHECKED_IN_KICKED",
        ];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray($inputArray));
    }

    public function testItGetsPartlyIntegerArray()
    {
        $expectedArray = [TournamentUserState::CHECKED_IN, TournamentUserState::REGISTERED_KICKED];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray(["CHECKED_IN", "REGISTERED_KICKED"]));
    }

    public function testItGetsStringArray()
    {
        $expectedArray = [
            "CHECKED_IN",
            "REGISTERED",
            "REGISTERED_KICKED",
            "CHECKED_IN_KICKED",
        ];

        $this->assertEquals($expectedArray, $this->record->getStringArray());
    }
}
