<?php

namespace App\Http\Utilities;

class FailureTypes {

    protected static $failureTypes = [
        '0'  => 'Select type',
        '1'  => 'N/A',
        '2'  => 'Communication',
        '3'  => 'Contractor Management',
        '4'  => 'Design',
        '5'  => 'Hardware',
        '6'  => 'Human Error - Slips/Lapse/Mistake',
        '7'  => 'Human Error - Violation',
        '8'  => 'Incompatible Goals',
        '9'  => 'Maintenance Management',
        '10' => 'Management of Change',
        '11' => 'Management Systems',
        '12' => 'Organisation',
        '13' => 'Organisation Culture',
        '14' => 'Organisational Learning',
        '15' => 'Procedures',
        '16' => 'Regulatory Influence',
        '17' => 'Risk Management',
        '18' => 'Training',
        '19' => 'Vehicle Management'
    ];


    /**
     * @return array
     */
    public static function all()
    {

        //$all = asort(static::$failureTypes);
        //return $all;
        return static::$failureTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$failureTypes[$id];
    }
}