<h3>New Toolbox Talk Template Created</h3>
<p>The following Toolbox Talk Template was created by {{ $user_fullname }} ({{ $user_company_name }}).</p>
<table style="border: none">
    <tr>
        <td width="100" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $talk_name }}</td>
    </tr>
    <tr>
        <td width="100" style="text-align: right"><b>Talk ID</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $talk_id }}</td>
    </tr>
    <tr>
        <td width="100" style="text-align: right">&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td><a href="{{ $talk_url }}"><br>View Talk</a></td>
    </tr>
</table>
<p><br><br>Regards,</p>
<p>Safeworksite</p>

<hr>
<p>This email has been generated on behalf of {{ $user_company_name }}</p>
