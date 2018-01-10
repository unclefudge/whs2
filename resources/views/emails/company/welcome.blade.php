@component('mail::message')
# Welcome to SafeWorksite

{{ $name }},

Your company {{ $company->name }} has been invited to join SafeWorksite by <b>{{ $parent_company->name }}</b>.

SafeWorksite is an online WHS platform to help you and your work mates stay safe. To be able to perform any work on a site managed by {{ $parent_company->name }} you are required to sign up and register any workers within your company.

@component('mail::button', ['url' => config('app.url').'/signup/ref/'.$company->signup_key])
Sign Up
@endcomponent

If you have any questions in regards to SafeWorksite you may contact {{ $parent_company->name }} on {{ $parent_company->phone }}

Regards,<br>
{{ config('app.name') }}
@endcomponent
