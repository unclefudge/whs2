<?php

namespace App\Models\Company;

use DB;
use URL;
use Mail;
use App\User;
use App\Models\Comms\Todo;
use App\Models\Company\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyDoc extends Model {

    protected $table = 'company_docs';
    protected $fillable = [
        'type', 'category_id', 'name', 'attachment', 'expiry', 'ref_no', 'ref_name', 'ref_type',
        'version', 'private', 'share', 'notes', 'for_company_id', 'company_id',
        'status', 'created_by', 'updated_by', 'approved_by', 'approved_at'];
    protected $dates = ['expiry', 'approved_at'];


    /**
     * A Company Doc is for a specific company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company\Company', 'for_company_id');
    }

    /**
     * A Company Doc is owned by a company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function owned_by()
    {
        return $this->belongsTo('App\Models\Company\Company', 'company_id');
    }

    /**
     * A Company Doc  was created by a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * A Document belongs to a Category.  (sometimes - Some Reports do)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Company\CompanyDocCategory', 'category_id');
    }


    /**
     * Create ToDoo for Company Doc to be approved and assign to given user(s)
     */
    public function createApprovalToDo($user_list)
    {
        $company = Company::findOrFail($this->for_company_id);
        $todo_request = [
            'type'       => 'company doc',
            'type_id'    => $this->id,
            'name'       => 'Company Document Approval Request - ' . $company->name_alias . ' (' . $this->name . ')',
            'info'       => 'Please approve uploaded document',
            'due_at'     => nextWorkDate(Carbon::today(), '+', 2)->toDateTimeString(),
            'company_id' => $this->company_id,
        ];

        // Create ToDoo and assign to Site Supervisors
        /*
        if ($user_list) {
            $todo = Todo::create($todo_request);
            $todo->assignUsers($user_list);
            $todo->emailToDo();
        }*/

    }

    /**
     * Create ToDoo for Expired Company Doc to be sent to company
     */
    public function createExpiredToDo($user_list, $expired)
    {
        $mesg = ($expired == true) ? "$this->name Expired " . $this->expiry->format('d/m/Y') : "$this->name due to expire " . $this->expiry->format('d/m/Y');
        $company = Company::findOrFail($this->for_company_id);
        $todo_request = [
            'type'       => 'company doc',
            'type_id'    => $this->id,
            'name'       => $mesg,
            'info'       => 'Please uploaded a current version of the document',
            'due_at'     => Carbon::today()->addDays(7)->toDateTimeString(),
            'company_id' => $this->company_id,
        ];

        // Create ToDoo and assign to Site Supervisors
        $todo = Todo::create($todo_request);
        $todo->assignUsers($user_list);
        $todo->emailToDo();
    }

    /**
     * Close any outstanding ToDoo for this Doc
     */
    public function closeToDo($user = '')
    {
        if (!$user)
            $user = Auth::user();

        // Get a list of Document ID's of same type as this Document ie Workers Comp
        // so we can close any ToDoo related to this type of document
        $similiar_docs = DB::table('company_docs')->select('id')->where('category_id', $this->category_id)->where('for_company_id', $this->for_company_id)->where('company_id', $this->company_id)->get();
        $id_array = [];
        foreach ($similiar_docs as $doc)
            $id_array[] = $doc->id;

        $todos = Todo::where('type', 'company doc')->whereIn('type_id', $id_array)->where('status', '1')->get();
        foreach ($todos as $todo) {
            $todo->status = 0;
            $todo->done_at = Carbon::now();
            $todo->done_by = $user->id;
            $todo->save();
        }
    }

    /**
     * Email document as Rejected
     */
    public function emailReject($email_list = '')
    {
        $email_to = [];
        // Send to User who created
        if (\App::environment('prod')) {
            if (validEmail($this->createdBy->email))
                $email_to[] = $this->createdBy->email;
        } else
            $email_to[] = env('EMAIL_ME');

        $email_user = (validEmail(Auth::user()->email)) ? Auth::user()->email : '';

        $data = [
            'user_email'        => Auth::user()->email,
            'user_fullname'     => Auth::user()->fullname,
            'user_company_name' => Auth::user()->company->name,
            'doc_name'          => $this->name,
            'doc_attachment'    => $this->attachment,
            'url'               => URL::to('/company') . '/' . $this->for_company_id,
        ];
        $doc = $this;
    }

    /**
     * Email document as Rejected
     */
    public function emailExpired($email_to = '', $expired)
    {
        $company = Company::find($this->for_company_id);
        if (\App::environment('prod')) {
            if (!$email_to) {
                $email_to = [];
                $email_to[] = $company->seniorUsersEmail();

                // Send CC to Parent Company Account
                $email_user = $company->reportsTo()->notificationsUsersEmailType('company.doc');
            }
        } else {
            $email_to = [env('EMAIL_ME')];
            $email_user = [env('EMAIL_ME')];
        }

        $mesg = ($expired == true) ? "has Expired " . $this->expiry->format('d/m/Y') : "due to expire " . $this->expiry->format('d/m/Y');

        $data = [
            'user_email'        => 'do-not-reply@safeworksite.com.au',
            'user_fullname'     => 'Safeworksite',
            'user_company_name' => 'Safeworksite',
            'company_name'      => $company->name_alias,
            'doc_name'          => $this->name,
            'doc_attachment'    => $this->attachment,
            'mesg'              => $mesg,
            'url'               => URL::to('/company') . '/' . $this->for_company_id,
        ];
        $doc = $this;
        /*
        Mail::send('emails/company-doc-expired', $data, function ($m) use ($email_to, $email_user, $doc, $mesg, $data) {
            $m->from('do-not-reply@safeworksite.com.au');
            $m->to($email_to);
            if ($email_user)
                $m->cc($email_user);
            $m->subject('Company Document ' . $doc->name . ' ' . $mesg);
        });*/
    }


    /**
     * Get the Attachment URL (setter)
     */
    public function getAttachmentUrlAttribute()
    {
        //$url = URL::to('/filebank') . '/company/' . $this->company->id . '/docs/' . $this->attributes['attachment'];
        if ($this->attributes['attachment'])// && file_exists(public_path('/filebank/company/' . $this->company->id . '/docs/' . $this->attributes['attachment'])))
            return '/filebank/company/' . $this->company->id . '/docs/' . $this->attributes['attachment'];

        return '';
    }

    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    /*public function getOwnedByAttribute()
    {
        return $this->company;
    }*/

    /**
     * Display records last update_by + date
     *
     * @return string
     */
    public function displayUpdatedBy()
    {
        $user = User::findOrFail($this->updated_by);

        return '<span style="font-weight: 400">Last modified: </span>' . $this->updated_at->diffForHumans() . ' &nbsp; ' .
        '<span style="font-weight: 400">By:</span> ' . $user->fullname;
    }

    /**
     * The "booting" method of the model.
     *
     * Overrides parent function
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        if (Auth::check()) {
            // create a event to happen on creating
            static::creating(function ($table) {
                $table->created_by = Auth::user()->id;
                $table->updated_by = Auth::user()->id;
            });

            // create a event to happen on updating
            static::updating(function ($table) {
                $table->updated_by = Auth::user()->id;
            });
        }
    }

}

