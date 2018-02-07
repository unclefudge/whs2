<?php

namespace App\Http\Utilities;

class CompanyEntityTypes {

    protected static $companyEntityTypes = [
        //''  => 'Select entity',
        '1' => 'Company',
        '2' => 'Partnership',
        '3' => 'Sole Trader',
        '4' => 'Trading Trust',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$companyEntityTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$companyEntityTypes[$id];
    }
}