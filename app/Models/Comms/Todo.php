<?php

namespace App\Models\Comms;

use DB;
use URL;
use Mail;
use App\User;
use App\Models\Misc\Action;
use App\Models\Company\Company;
use App\Models\Company\CompanyDoc;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use nilsenj\Toastr\Facades\Toastr;

class Todo extends Model {

    protected $table = 'todo';
    protected $fillable = [
        'name', 'info', 'comments', 'type', 'type_id', 'due_at', 'done_at', 'done_by',
        'priority', 'attachment', 'status', 'company_id', 'created_by', 'updated_by'
    ];

    protected $dates = ['due_at', 'done_at'];

    /**
     * A Todoo belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * A Todoo belongs to a company
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company\Company');
    }

    /**
     * A Todoo is assigned to many Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Models\Comms\TodoUser', 'todo_id');
    }

    /**
     * A Todoo is assigned to many Users
     *
     * @return collection of users
     */
    public function assignedTo()
    {
        $user_list = $this->users->pluck('user_id')->toArray();

        return User::whereIn('id', $user_list)->get();
    }

    /**
     * A Todoo is assigned to many users - return list separated by comma
     *
     * return string
     */
    public function assignedToBySBC()
    {
        $string = '';
        foreach ($this->assignedTo() as $user) {
            $string .= $user->fullname . ', ';
        }
        $string = rtrim($string, ', ');

        return $string;
    }

    /**
     * A Todoo is done 'completed' by a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function doneBy()
    {
        return $this->belongsTo('App\User', 'done_by');
    }


    /**
     * A Todoo is often linked to a webpage
     *
     * @return url
     */
    public function url()
    {
        switch ($this->type) {
            case 'toolbox':
                return '/safety/doc/toolbox2/' . $this->type_id;
            case 'qa':
                return '/site/qa/' . $this->type_id;
            case 'hazard':
                return '/todo/' . $this->id;
            case 'swms':
                return '/todo/' . $this->id;
            case 'company doc':
                $doc = CompanyDoc::find($this->type_id);
                if ($doc) {
                    $company = Company::find($doc->for_company_id);

                    return '/company/' . $company->id;
                }
            case 'general':
                return '/todo/' . $this->id;
        }

        return '';
    }

    /**
     * Assign a list of users to the ToDo
     */
    public function assignUsers($user_ids)
    {
        if (is_array($user_ids))
            foreach ($user_ids as $user_id) {
                TodoUser::create(['todo_id' => $this->id, 'user_id' => $user_id]); // Assign users
            }
        elseif ($user_ids)
            TodoUser::create(['todo_id' => $this->id, 'user_id' => $user_ids]); // Assign users

    }


    /**
     * A Notify 'may' have been opened by multiple users
     *
     * return collection
     */
    public function openedBy()
    {
        $user_ids = TodoUser::where('todo_id', $this->id)->where('opened', 1)->pluck('user_id')->toArray();

        return User::whereIn('id', $user_ids)->orderBy('firstname')->get();
    }

    /**
     * A Todoo is assigned to many users - return list separated by comma
     *
     * return string
     */
    public function openedBySBC()
    {
        $string = '';
        foreach ($this->assignedTo() as $user) {
            if ($this->isOpenedBy($user)) {
                $todo_user = TodoUser::where('todo_id', $this->id)->where('user_id', $user->id)->where('opened', 1)->first();
                $string .= $user->fullname . ' (' . $todo_user->opened_at->format('j/n/y') . '), ';
            } else
                $string .= $user->fullname . ', ';
        }
        $string = rtrim($string, ', ');

        return $string;
    }

    /**
     * Has a Todoo been opened by User (x)
     *
     * return booleen
     */
    public function isOpenedBy($user)
    {
        $record = TodoUser::where('todo_id', $this->id)->where('user_id', $user->id)->first();

        if ($record && $record->opened)
            return true;

        return false;
    }

    /**
     * Marked Todoo opened by User (x)
     */
    public function markOpenedBy($user)
    {
        $record = TodoUser::where('todo_id', $this->id)->where('user_id', $user->id)->first();

        if ($record && !$record->opened) {
            $record->opened = 1;
            $record->opened_at = Carbon::now();
            $record->save();
        }
    }

    /**
     * Save attached Media to existing Issue
     */
    public function saveAttachedMedia($file)
    {
        /*
        $site = Site::findOrFail($this->site_id);
        $path = "filebank/site/" . $site->id . '/issue';
        $name = 'issue-' . $site->code . '-' . $this->id . '-' . Auth::user()->id . '-' . sha1(time()) . '.' . strtolower($file->getClientOriginalExtension());
        $path_name = $path . '/' . $name;
        $file->move($path, $name);

        // resize the image to a width of 1024 and constrain aspect ratio (auto height)
        if (exif_imagetype($path_name)) {
            Image::make(url($path_name))
                ->resize(1024, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($path_name);
        } else
            Toastr::error("Bad image");

        $this->attachment = $name;
        $this->save();
        */
    }

    /**
     * Email ToDoo
     */
    public function emailToDo($email_list = '')
    {
        if (\App::environment('prod')) {
            if (!$email_list) {
                $email_list = [];
                foreach ($this->assignedTo() as $user) {
                    if (validEmail($user->email))
                        $email_list[] = $user->email;
                }
            }
        } else if (\App::environment('local', 'dev'))
            $email_list = [env('EMAIL_ME')];


        $email_user = (\App::environment('prod')) ? Auth::user()->email : '';
        $user_fullname = 'Safeworksite';
        $user_company_name = 'Safeworksite';
        if (Auth::check()) {
            $user_fullname = Auth::user()->fullname;
            $user_company_name = Auth::user()->company->name;
        }

        $due_at = 'N/A';
        if ($this->due_at)
            $due_at = $this->due_at->format('d/m/Y');

        // Determine if overdue
        $overdue = '';
        if ($due_at != 'N/A' && $this->due_at->lt(Carbon::today())) {
            $overdue = ' - OVERDUE';
        }
        $data = [
            'id'                => $this->id,
            'name'              => $this->name,
            'info'              => $this->info,
            'url'               => URL::to('/') . $this->url(),
            'due_at'            => $due_at,
            'overdue'           => $overdue,
            'user_fullname'     => $user_fullname,
            'user_company_name' => $user_company_name,
        ];

        Mail::send('emails/todo', $data, function ($m) use ($email_list, $email_user, $overdue) {
            $m->from('do-not-reply@safeworksite.net');
            $m->to($email_list);
            if (validEmail($email_user))
                $m->cc($email_user);
            $m->subject('ToDo Task Notification' . $overdue);
        });
    }

    /**
     * Email ToDoo
     */
    public function emailToDoCompleted($email_list = '')
    {
        //$email_list = env('EMAIL_ME');
        if (!$email_list) {
            $email_list = [];
            if (\App::environment('prod')) {
                foreach ($this->assignedTo() as $user) {
                    if (validEmail($user->email))
                        $email_list[] = $user->email;
                }
            } else if (\App::environment('local', 'dev')) {
                $email_list = [env('EMAIL_ME')];
            }
        }

        $email_user = (\App::environment('prod', 'dev')) ? Auth::user()->email : '';
        $user_fullname = 'Safeworksite';
        $user_company_name = 'Safeworksite';
        if (Auth::check()) {
            $user_fullname = Auth::user()->fullname;
            $user_company_name = Auth::user()->company->name;
        }

        $data = [
            'id'                => $this->id,
            'name'              => $this->name,
            'info'              => $this->info,
            'url'               => URL::to('/') . $this->url(),
            'done_at'           => $this->done_at->format('d/m/Y'),
            'done_by'           => User::find($this->done_by)->fullname,
            'user_fullname'     => $user_fullname,
            'user_company_name' => $user_company_name,
        ];

        Mail::send('emails/todo-completed', $data, function ($m) use ($email_list, $email_user) {
            $m->from('do-not-reply@safeworksite.net');
            $m->to($email_list);
            if (validEmail($email_user))
                $m->cc($email_user);
            $m->subject('ToDo Task Completed');
        });
    }


    /**
     * Get the owner of record   (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->createdBy;
    }

    /**
     * Get the Attachment URL (setter)
     */
    public function getAttachmentUrlAttribute()
    {
        if ($this->attributes['attachment'] && file_exists(public_path('/filebank/todo/' . $this->attributes['attachment'])))
            return '/filebank/todo/' . $this->attributes['attachment'];

        return '';
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