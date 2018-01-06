<?php

namespace App\Http\Utilities;

class CompanyTypes {

    protected static $companyTypes = [
        ''                      => 'Select category',
        'Consultants/Externals' => 'Consultants/Externals',
        'Guest'                 => 'Guest',
        'Internal Staff'        => 'Internal Staff',
        'Inspector/Certifier'   => 'Inspector/Certifier',
        'Office Use'            => 'Office Use',
        'On Site Trade'         => 'On Site Trade',
        'Supplier'              => 'Supplier',
        'Web Development'       => 'Web Development',
        'Unallocation'          => 'Unallocation'
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