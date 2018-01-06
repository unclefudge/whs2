<h3>SWMS - {{ $mesg }}</h3>
<table style="border: none">
    <tr>
        <td width="50" style="text-align: right"><b>Company</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $company_name }}</td>
    </tr>
    <tr>
        <td width="50" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $doc_name }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View SWMS</a></td>
    </tr>
</table>

<hr>
<p>This email has been generated on behalf of {{ $user_company_name }}</p>
