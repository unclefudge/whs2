<?php

namespace App\Http\Utilities;

class LicenceTypes {

    protected static $licenceTypes = [
        '0'  => 'Select type',
        '1'  => 'Air conditioning',
        '2'  => 'Bricklaying',
        '3'  => 'Building',
        '4'  => 'Carpentry',
        '5'  => 'Decorating',
        '6'  => 'Demolishing',
        '7'  => 'Dry plastering',
        '8'  => 'Electrical',
        '9'  => 'Excavating',
        '10' => 'Fencing',
        '11' => 'Flooring',
        '12' => 'General concreting',
        '13' => 'Glazing',
        '14' => 'Joinery',
        '15' => 'Mechanical services',
        '16' => 'Metal fabrication',
        '17' => 'Minor maintenance/cleaning',
        '18' => 'Minor tradework',
        '19' => 'Painting',
        '20' => 'Plumbing',
        '21' => 'Roof plumbing',
        '22' => 'Roof slating',
        '23' => 'Roof tiling',
        '24' => 'Scaffolding',
        '25' => 'Stonemasonry',
        '26' => 'Underpinning/piering',
        '27' => 'Wall and floor tiling',
        '28' => 'Waterproofing',
        '29' => 'Wet plastering',
    ];


    /**
     * @return array
     */
    public static function all()
    {

        //$all = asort(static::$licenceTypes);
        //return $all;
        return static::$licenceTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$licenceTypes[$id];
    }

    /**
     * @return string
     */
    public static function allSBC()
    {
        $string = '';
        for ($i=1; $i < 30; $i++)
            $string .= static::$licenceTypes[$i].", ";

        return rtrim($string, ', ');
    }
}