@component('mail::message')
# Nightly Job

The nightly job {{ $status }}

Regards,<br>
{{ config('app.name') }}
@endcomponent