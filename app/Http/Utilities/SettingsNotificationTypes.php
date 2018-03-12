<?php

namespace App\Http\Utilities;

class SettingsNotificationTypes {

    protected static $settingsNotificationTypes = [
        '1' => 'company.doc',
        '2' => 'whs',
        '3' => 'site.accident',
        '4' => 'site.hazard',
        '5' => 'site.asbestos',
        '6' => 'site.qa',
        '7' => 'company.signup',
        '8' => 'user.created',
        '9' => 'company.created',
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