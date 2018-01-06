<h3>Quality Assurance Notification</h3>
<p>A Quality Assurance has been updated with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Site</b></td>
        <td >&nbsp;</td>
        <td>{{ $site_name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Supervisor(s)</b></td>
        <td >&nbsp;</td>
        <td>{{ $supers }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Date/Time</b></td>
        <td>&nbsp;</td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Submitted by</b></td>
        <td>&nbsp;</td>
        <td>{{ $user_fullname }} ({{ $user_company_name }})</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Action Taken</b></td>
        <td>&nbsp;</td>
        <td>{{ $action }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View QA</a></td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $site_owner }}</p>
