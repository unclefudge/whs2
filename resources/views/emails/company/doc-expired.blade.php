@component('mail::message')
# Company Document {!! ($doc->expiry->lt(Carbon::today)) ? "has Expired " . $doc->expiry->format('d/m/Y') : "due to expire " . $doc->expiry->format('d/m/Y'); !!}

{{ $doc->owned_by->name }} has not approved your document {{ $doc->name }} for the following reason:

{!! nl2br($doc->reject) !!}


Please review the document and correct it.

If you have any questions in regards to the document you may contact {{ $doc->owned_by->name }} on {{ $doc->owned_by->phone }}

@component('mail::button', ['url' => config('app.url').'/company/'.$doc->company_id.'/doc/'.$doc->id.'/edit'])
View Document
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent