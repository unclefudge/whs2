<?php

namespace App\Http\Utilities;

class CompanyTypes {

    protected static $companyTypes = [
        '1' => 'Subcontractor (On Site Trade)',
        '2' => 'Service Provider',
        '3' => 'Supply & Fit',
        '4' => 'Supply Only',
        '5' => 'Consultant',
        '6' => 'Internal Staff',
        '7' => 'Unallocated',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$companyTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$companyTypes[$id];
    }
}