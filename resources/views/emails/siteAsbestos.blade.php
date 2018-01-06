<h3>Asbestos Notification</h3>
<p>An notification has been lodged with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Notification ID</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $id }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Site</b></td>
        <td >&nbsp;</td>
        <td>{{ $site }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Address</b></td>
        <td>&nbsp;</td>
        <td>{{ $address }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Removal dates</b></td>
        <td>&nbsp;</td>
        <td>{{ $dates }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Amount</b></td>
        <td>&nbsp;</td>
        <td>{{ $amount }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Class</b></td>
        <td>&nbsp;</td>
        <td>{{ $class }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Type</b></td>
        <td>&nbsp;</td>
        <td>{{ $type }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Location</b></td>
        <td>&nbsp;</td>
        <td>{{ $location }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Submitted by</b></td>
        <td>&nbsp;</td>
        <td>{{ $user_fullname }} ({{ $user_company_name }})</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View Notification</a></td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $site_owner }}</p>
