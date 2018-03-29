@component('mail::message')
# Company Document {!! ($doc->expiry->lt(\Carbon\Carbon::today())) ? "has Expired " : "due to expire " !!}

{{ $doc->company->name }} document **{{ $doc->name }}** {!! ($doc->expiry->lt(\Carbon\Carbon::today())) ? "has Expired " . $doc->expiry->format('d/m/Y') : "due to expire " . $doc->expiry->format('d/m/Y'); !!}

Please follow up with {{ $doc->company->name }} and ensure they upload a current version.

@component('mail::button', ['url' => config('app.url').'/company/'.$doc->for_company_id.'/doc'])
View Documents
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent