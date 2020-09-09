<?php

namespace App\Http\Utilities;

class OzStates {

    protected static $ozstates = [
        'ACT' => 'ACT',
        'NSW' => 'NSW',
        'NT'  => 'NT',
        'QLD' => 'QLD',
        'SA'  => 'SA',
        'TAS' => 'TAS',
        'VIC' => 'VIC',
        'WA'  => 'WA'
    ];

    /**
     * @return array
     */
    public static function all()
    {
        return static::$ozstates;
    }
}