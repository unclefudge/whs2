<h3>Quality Assurance Overdue Notification</h3>
<p>The following Quality Assurance report is currently overdue:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Site</b></td>
        <td>&nbsp;</td>
        <td>{{ $site_name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Supervisor(s)</b></td>
        <td >&nbsp;</td>
        <td>{{ $supers }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View QA</a></td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $user_fullname }}</p>
