<?php

namespace App\Http\Utilities;

class MaintenanceWarranty {

    protected static $maintenanceWarranty = [
        ''  => 'Select type',
        'Defect'  => 'Defect (12 mths)',
        'Struct'  => 'Structural (10yrs)',
        'Prac'  => 'Outstanding Prac',
        'Other'  => 'Other',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$maintenanceWarranty;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$maintenanceWarranty[$id];

        return $id;
    }
}