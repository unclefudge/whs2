@component('mail::message')
# New Company

{{ $company->name }} has joined SafeWorksite and completed the sign up process.

|        |        |
| ------:|--------|
| **Name**  | {{ $company->name  }} |
| **Phone**  | {{ $company->phone  }} |
| **Email**  | {{ $company->email  }} |
| **Address**  | {{ $company->address  }} {{ $company->SuburbStatePostcode }} |
| **Primary Contact**  | {{ $company->primary_contact()->fullname  }} @if ($company->phone){{ $company->primary_contact()->phone  }}) @endif |
| **Created By** | {{ $company->createdBy->name  }} |

----

The following users were created:

|  Username  | Name  | Email  | Phone
| -----------|-------|--------|--------|
@foreach ($company->staffStatus(1) as $staff)
| {{ $staff->username }}  | {{ $staff->fullname }} | {{ $staff->email }} | {{ $staff->phone }}
@endforeach


@component('mail::button', ['url' => config('app.url').'/company/'.$company->id])
View Company
@endcomponent

Regards,<br>
{{ config('app.name') }}
@endcomponent
