<?php

namespace App\Http\Utilities;

class MaintenanceCategories {

    protected static $maintenanceCategories = [
        //''  => 'Select entity',
        '0' => 'Select Category',
        '1' => 'Doors',
        '2' => 'Electrical',
        '3' => 'Ext Cladding',
        '4' => 'Floor',
        '5' => 'Gyprock',
        '6' => 'Internal Fixing',
        '7' => 'Other',
        '8' => 'Plumbing',
        '9' => 'Rain Damage',
        '10' => 'Roof Damage',
        '11' => 'Stairs',
        '12' => 'Tiling',
        '13' => 'Water Leaks',
        '14' => 'Windows',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$maintenanceCategories;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        if (preg_match('/[0-9]+/', $id))
            return static::$maintenanceCategories[$id];

        return $id;
    }
}