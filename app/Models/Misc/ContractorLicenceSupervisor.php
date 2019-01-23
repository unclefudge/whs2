<?php

namespace App\Models\Misc;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ContractorLicenceSupervisor extends Model {

    protected $table = 'contractor_licence_supervisors';
    protected $fillable = ['doc_id', 'super', 'licence_id', 'user_id', 'company_id'];


    /**
     * A Licence class belongs to a CompanyDoc
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function doc()
    {
        return $this->belongsTo('App\Models\Company\CompanyDoc', 'doc_id');
    }

    /**
     * A Licence class belongs to a ContractorLicence
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function licence()
    {
        return $this->belongsTo('App\Models\Misc\ContractorLicence', 'licence_id');
    }

    /**
     * A Licence class belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
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