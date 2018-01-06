<h3>Toolbox Talk Overdue Notification</h3>
<p>The following Toolbox Talk is outstanding for several users.</p>
<table style="border: none">
    <tr>
        <td width="130" style="text-align: right"><b>ID</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $talk_id }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Name</b></td>
        <td>&nbsp;</td>
        <td>{{ $talk_name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Completed by</b></td>
        <td>&nbsp;</td>
        <td>{{ $talk_count }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Users Outstanding</b></td>
        <td>&nbsp;</td>
        <td>{{ $talk_outstanding }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td>&nbsp;</td>
        <td><a href="{{ $talk_url }}"><br>View Talk</a></td>
    </tr>
</table>
<p><br><br>Regards,</p>
<p>Safeworksite</p>

<hr>
<p>This email has been generated on behalf of {{ $user_company_name }}</p>
