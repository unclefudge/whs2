<?php

namespace App\Models\Misc;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContractorLicence extends Model {

    protected $table = 'contractor_licence';
    protected $fillable = ['type', 'name', 'parent', 'status', 'notes'];


    /**
     * A Licence has many sub classes
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function subClass()
    {
        return $this->hasMany('App\Models\Misc\ContractorLicence', 'parent');
    }

    /**
     * A Licence has a Parent class.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parentClass()
    {
        return $this->belongsTo('App\Models\Misc\ContractorLicence', 'parent');
    }


    /**
     * A dropdown of Class Option.
     *
     * @return string
     */
    public function classOptions($selected = [])
    {
        $str = '<option></option>';
        $main_classes = ContractorLicence::where('parent', 0)->get();
        foreach ($main_classes as $main) {
            $str .= "<optgroup label='$main->name'>";
            $classes = ContractorLicence::where('parent', $main->id)->get();
            foreach ($classes as $class) {
                $sel = (in_array($class->id, $selected)) ? 'selected' : '';
                $str .= "<option value='$class->id' $sel>$class->name</option>";
            }
        }
        return $str;
    }
}