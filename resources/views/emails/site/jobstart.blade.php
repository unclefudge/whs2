@component('mail::message')

# Job Start Notification

{!! ($olddate) ? "An Job Start has been moved on **$site->name** from $olddate to $newdate." : "An Job Start has been created for **$site->name** on $newdate."!!}

@component('mail::button', ['url' => config('app.url').'/planner/site/'.$site->id])
View Planner
@endcomponent


Regards,<br>
{{ config('app.name') }}
@endcomponent
