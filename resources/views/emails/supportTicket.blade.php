<h3>Suport Ticket Notification</h3>
<p>An support ticket has been lodged with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Ticket ID</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $id }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Priority</b></td>
        <td>&nbsp;</td>
        <td>{{ $priority }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Name</b></td>
        <td >&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Description</b></td>
        <td>&nbsp;</td>
        <td>{!! nl2br($summary) !!}</td>
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
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $user_company_name }}</p>
