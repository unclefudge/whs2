<h3>ToDo Notification {{ $overdue }}</h3>
<p>A task has been sent to you with the following details:</p>
<table style="border: none">
    <tr>
        <td width="120" style="text-align: right"><b>Name</b></td>
        <td width="20">&nbsp;</td>
        <td>{{ $name }}</td>
    </tr>
    <tr>
        <td style="text-align: right"><b>Due Date</b></td>
        <td>&nbsp;</td>
        <td>{{ $due_at }}</td>
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
