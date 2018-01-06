<h3>New User</h3>
<p>A new user has been created on the system with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Date</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Username</b></td>
        <td>&nbsp;</td>
        <td>{{ $username }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Name</b></td>
        <td>&nbsp;</td>
        <td>{{ $fullname }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Company</b></td>
        <td>&nbsp;</td>
        <td>{{ $company_name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Created by</b></td>
        <td>&nbsp;</td>
        <td>{{ $created_by }}</td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $site_owner }}</p>
