<?php

namespace Tests\Unit;

use App\Http\Enums\UserType;
use Tests\TestCase;

class UserTypeTest extends TestCase
{
    private $record;

    protected function setUp(): void
    {
        parent::setUp();

        $this->record = new UserType();
    }

    public function testItGetsStringOfInteger()
    {
        $this->assertEquals("SUPERADMIN", $this->record->getStringOfInteger(UserType::SUPERADMIN));
        $this->assertEquals("STREAMER", $this->record->getStringOfInteger(UserType::STREAMER));
        $this->assertEquals("MEMBER", $this->record->getStringOfInteger(UserType::MEMBER));
        $this->assertEquals("GUEST", $this->record->getStringOfInteger(UserType::GUEST));
        $this->assertEquals("BANNED", $this->record->getStringOfInteger(UserType::BANNED));
        $this->assertEquals("UNDEFINED", $this->record->getStringOfInteger(-1));
    }

    public function testItGetsIntegerOfString()
    {
        $this->assertEquals(UserType::SUPERADMIN, $this->record->getIntegerOfString("SUPERADMIN"));
        $this->assertEquals(UserType::STREAMER, $this->record->getIntegerOfString("STREAMER"));
        $this->assertEquals(UserType::MEMBER, $this->record->getIntegerOfString("MEMBER"));
        $this->assertEquals(UserType::GUEST, $this->record->getIntegerOfString("GUEST"));
        $this->assertEquals(UserType::BANNED, $this->record->getIntegerOfString("BANNED"));
    }

    public function testItGetsFullIntegerArray()
    {
        $expectedArray = [
            UserType::SUPERADMIN,
            UserType::STREAMER,
            UserType::MEMBER,
            UserType::GUEST,
            UserType::BANNED,
        ];
        $inputArray = [
            "SUPERADMIN",
            "STREAMER",
            "MEMBER",
            "GUEST",
            "BANNED",
        ];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray($inputArray));
    }

    public function testItGetsPartlyIntegerArray()
    {
        $expectedArray = [UserType::SUPERADMIN, UserType::MEMBER];

        $this->assertEquals($expectedArray, $this->record->getIntegerArray(["SUPERADMIN", "MEMBER"]));
    }

    public function testItGetsStringArray()
    {
        $expectedArray = [
            "SUPERADMIN",
            "STREAMER",
            "MEMBER",
            "GUEST",
            "BANNED",
        ];

        $this->assertEquals($expectedArray, $this->record->getStringArray());
    }
}
