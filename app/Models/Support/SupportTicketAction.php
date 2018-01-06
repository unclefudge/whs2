<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Mail;

class SupportTicketAction extends Model {

    protected $table = 'support_tickets_actions';
    protected $fillable = ['ticket_id', 'action', 'attachment', 'created_by', 'created_at'];

    /**
     * A SupportTicketAction belongs to a site issue
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        return $this->belongsTo('App\Models\Support\SupportTicket', 'ticket_id');
    }

    /**
     * A SupportTicketAction belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Save attachment to existing Issue
     */
    public function saveAttachment($file)
    {
        $path = "filebank/support/ticket/";
        $name = 'ticket-' . $this->ticket->id . '-' . Auth::user()->id . '-' . sha1(time()) . '.' . strtolower($file->getClientOriginalExtension());
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
        }

        $this->attachment = $name;
        $this->save();
    }

    /**
     * Email Action Notification
     */
    public function emailAction($action)
    {
        $ticket = SupportTicket::findOrFail($action->ticket_id);

        $email_list = env('EMAIL_ME');
        if (\App::environment('prod', 'dev'))
            $email_list = "jo@capecod.com.au; tara@capecod.com.au; ".$email_list;
        $email_list = explode(';', $email_list);
        $email_list = array_map('trim', $email_list); // trim white spaces
        $email_user =  $ticket->createdBy->email;
        $data = [
            'id'                => $ticket->id,
            'date'              => Carbon::now()->format('d/m/Y g:i a'),
            'name'              => $ticket->name,
            'priority'          => $ticket->priority_text,
            'summary'           => $this->action,
            'user_fullname'     => Auth::user()->fullname,
            'user_company_name' => Auth::user()->company->name,
        ];
        $filename = $this->attachment;
        Mail::send('emails/supportTicket', $data, function ($m) use ($email_list, $email_user, $filename, $action) {
            $m->from('do-not-reply@safeworksite.net');
            $m->to($email_list);
            if ($email_user)
                $m->cc($email_user);
            $m->subject('Support Ticket Update Notification');
            $file_path = public_path('filebank/support/ticket/'.$filename);
            if ($filename && file_exists($file_path))
                $m->attach($file_path);
        });
    }

    /**
     * Get the owner of record  (getter)
     *
     * @return string;
     */
    public function getOwnedByAttribute()
    {
        return $this->ticket->company;
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