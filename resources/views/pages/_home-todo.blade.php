<li>
    <a href="{{ $todo->url() }}" class="task-title">
        <div class="col1">
            <div class="cont">
                <div class="cont-col1">
                    <div class="label label-sm @if($todo->priority) label-danger @else label-success @endif">
                        <i class="fa fa-star"></i>
                    </div>
                </div>
                <div class="cont-col2">
                    <div class="desc"> {{ $todo->name }}</div>
                </div>
            </div>
        </div>
        <div class="col2">
            <div class="date"> {!! ($todo->due_at) ? $todo->due_at->format('d/m/Y') : '-'!!}</div>
        </div>
    </a>
</li>