<?php

namespace App\Http\Enums;

use Exception;
use ReflectionClass;

class TournamentEntryLevel
{
    const MINIMUM_SUBSCRIBER = 20;
    const MINIMUM_FOLLOWER = 10;
    const NONE = 0;

    /**
     * @param int $integer
     * @return string
     */
    public function getStringOfInteger(int $integer): string
    {
        try {
            $thisClass = new ReflectionClass(TournamentEntryLevel::class);
            $constants = $thisClass->getConstants();

            foreach ($constants as $key => $value) {
                if ($value == $integer)
                    return $key;
            }
        } catch (Exception $exception) {
        }

        return "UNDEFINED";
    }

    /**
     * @param string $string
     * @return int|null
     */
    public function getIntegerOfString(string $string): ?int
    {
        try {
            $thisClass = new ReflectionClass(TournamentEntryLevel::class);
            $constants = $thisClass->getConstants();

            foreach ($constants as $key => $value) {
                if ($key == $string)
                    return $value;
            }
        } catch (Exception $exception) {
        }

        return null;
    }

    /**
     * @param array $strings
     * @return array
     */
    public function getIntegerArray(array $strings): array
    {
        $values = [];

        for ($i = 0; $i < count($strings); $i++) {
            $values[] = $this->getIntegerOfString($strings[$i]);
        }

        return $values;
    }

    /**
     * @return array
     */
    public function getStringArray(): array
    {
        $values = [];

        $thisClass = new ReflectionClass(TournamentEntryLevel::class);
        $constants = $thisClass->getConstants();

        foreach ($constants as $key => $value) {
            $values[] = $key;
        }

        return $values;
    }
}
