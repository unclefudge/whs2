<h3>ToDo Task Completed</h3>
<p>The task with the following details has been completed:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Completed</b></td>
        <td>&nbsp;</td>
        <td>{{ $done_at }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Completed by</b></td>
        <td>&nbsp;</td>
        <td>{{ $done_by }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Details</b></td>
        <td >&nbsp;</td>
        <td>{{ $info }}</td>
    </tr>
    <tr>
        <td style="text-align: right">&nbsp;</td>
        <td >&nbsp;</td>
        <td><a href="{{ $url }}">View Task</a></td>
    </tr>
</table>
<br>
<hr>
<p>This email has been generated on behalf of {{ $user_fullname }}</p>
