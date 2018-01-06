<h3>WHS Accident Notification</h3>
<p>An accident has been lodged with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>ID</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $id }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Site</b></td>
        <td>&nbsp;</td>
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
        <td style="text-align: right"><b>Worker</b></td>
        <td>&nbsp;</td>
        <td>{{ $worker }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Occupation</b></td>
        <td>&nbsp;</td>
        <td>{{ $occupation }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Location</b></td>
        <td>&nbsp;</td>
        <td>{{ $location }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Nature</b></td>
        <td>&nbsp;</td>
        <td>{{ $nature }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Referred to</b></td>
        <td>&nbsp;</td>
        <td>{{ $referred }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Damage</b></td>
        <td>&nbsp;</td>
        <td>{{ $damage }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Description</b></td>
        <td>&nbsp;</td>
        <td>{{ $description }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Submitted by</b></td>
        <td>&nbsp;</td>
        <td>{{ $user_fullname }} ({{ $user_company_name }})</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Submitted at</b></td>
        <td>&nbsp;</td>
        <td>{{ $submit_date }}</td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $site_owner }}</p>
