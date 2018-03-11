<?php

namespace App\Http\Utilities;

use DB;

class CompanyDocTypes {

    protected static $companyDocTypes = [
        'acc' => 'Accounting',
        'adm' => 'Administation',
        'con' => 'Contruction',
        'whs' => 'WHS',
    ];


    /**
     * @return array
     */
    public static function all()
    {
        return static::$companyDocTypes;
    }

    /**
     * @return string
     */
    public static function name($id)
    {
        return static::$companyDocTypes[$id];
    }

    /**
     * @return array
     */
    public static function docs($type, $private = 0)
    {
        return DB::table('company_docs_categories')->where('type', $type)->where('private', $private)->get();
    }

    /**
     * @return array
     */
    public static function docCats($type, $private = 0)
    {
        $ids = [];
        $docs = DB::table('company_docs_categories')->where('type', $type)->where('private', $private)->get();
        foreach ($docs as $doc) {
            $ids[] = $doc->id;
        }

        return $ids;
    }

    /**
     * @return array
     */
    public static function docNames($type, $private = 0)
    {
        $names = '';
        $docs = DB::table('company_docs_categories')->where('type', $type)->where('private', $private)->get();
        foreach ($docs as $doc) {
            $names .= "$doc->name, ";
        }

        return rtrim($names, ', ');
    }
}