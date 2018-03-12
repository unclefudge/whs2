<?php

namespace App\Http\Utilities;

class SettingsNotificationTypes {

    protected static $settingsNotificationTypes = [
        '1' => 'spare',
        '2' => 'spare',
        '3' => 'n.site.accident',
        '4' => 'n.site.hazard',
        '5' => 'n.site.asbestos',
        '6' => 'n.site.qa',
        '7' => 'n.company.signup',
        '8' => 'n.user.created',
        '9' => 'n.company.created',
        '10' => 'n.docs.acc.pub',
        '11' => 'n.docs.adm.pub',
        '12' => 'n.docs.con.pub',
        '13' => 'n.doc.whs.pub',
        '14' => 'n.docs.acc.pri',
        '15' => 'n.docs.adm.pri',
        '16' => 'n.docs.con.pri',
        '17' => 'n.doc.whs.pri',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$settingsNotificationTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$settingsNotificationTypes[$id];
    }

    /**
     * @return string
     */
    public static function type($name)
    {
        return array_search($name, static::$settingsNotificationTypes);
    }
}