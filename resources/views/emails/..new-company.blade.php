<h3>New Company</h3>
<p>A new company has been created on the system with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Date</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $date }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Name</b></td>
        <td>&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Address</b></td>
        <td>&nbsp;</td>
        <td>{{ $address }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Phone</b></td>
        <td>&nbsp;</td>
        <td>{{ $phone }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Email</b></td>
        <td>&nbsp;</td>
        <td>{{ $email }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Created by</b></td>
        <td>&nbsp;</td>
        <td>{{ $created_by }}</td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $user_company }}</p>
