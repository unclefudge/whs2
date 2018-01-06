<h3>WHS Hazard Notification</h3>
<p>A hazard has been lodged with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Hazard ID</b></td>
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
        <td style="text-align: right"><b>Reason</b></td>
        <td>&nbsp;</td>
        <td>{{ $reason }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Actions Taken</b></td>
        <td>&nbsp;</td>
        <td>{{ $action }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View Hazard</a></td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $site_owner }}</p>
