<?php

namespace App\Http\Utilities;

class PayrollTaxTypes {

    protected static $payrollTaxTypes = [
        '0'   => 'Select type',
        '1'   => '1) Services ancillary to the supply of goods - provision of materials/equip is more than 50% of total contract amount - must be evidence to substantiate materials/equipment is principal object of contract',
        '2'   => '2) Services not associated with mainstream business activities and are available to public',
        '3'   => '3) Particular type of service required by CC is less than 180 days',
        '4'   => '4) Worked for Principal for less than <90 days in financial year',
        '5'   => '5) Genuine independant business - Contractor provides services of that kind to the public generally, must supply to 2 or more principals AND for an average of 10 days or less per month',
        '6'   => '6) 2+ employees worked onsite',
        '7'   => '7) Conveyance of goods in Contractor owned vehicle',
        '8'   => '8) Liable',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$payrollTaxTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$payrollTaxTypes[$id];
    }
}