<?php

namespace App\Http\Utilities;

class CompanyTypes {

    protected static $companyTypes = [
        '0' => 'Unallocated',
        '1' => 'Subcontractor (On Site Trade)',
        '2' => 'Service Provider (On Site Trade)',
        '3' => 'Service Provider (Off Site)',
        '4' => 'Supply & Fit',
        '5' => 'Supply Only',
        '6' => 'Consultant',
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
        return ($id != null) ? static::$companyTypes[$id] : '';
    }
}