<?php

namespace App\Http\Utilities;

class MaintenanceWarranty {

    protected static $maintenanceWarranty = [
        ''  => 'Select type',
        'DEFECT'  => 'Defect (12 mths)',
        'STRUCT'  => 'Structural (10yrs)',
        'PRAC'  => 'Outstanding Prac',
        'OTHER'  => 'Other',
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