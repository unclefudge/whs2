<h3>Company Document Not Approved</h3>
<p>The following Company Document is was not approved by {{ $user_fullname }} ({{ $user_company_name }}).</p>
<table style="border: none">
    <tr>
        <td width="50" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $doc_name }}</td>
    </tr>
    <tr>
        <td width="50" style="text-align: right"><b>Document</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $doc_attachment }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View Company Profile</a></td>
    </tr>
</table>
<p><br><br>Regards,</p>
<p>{{ $user_fullname }}</p>

<hr>
<p>This email has been generated on behalf of {{ $user_company_name }}</p>
