@if ($main->docs->count())
    <?php $doc_count = 0; ?>
    @foreach ($main->docs as $doc)
        <div style="width: 60px">
            <a href="{{ $doc->AttachmentUrl }}" class="html5lightbox " title="{{ $doc->name }}" data-lityXXX>
                <img src="{{ $doc->AttachmentUrl }}" class="thumbnail img-responsive img-thumbnail"></a>
        </div>
        <?php $doc_count ++; ?>
        @if ($doc_count == 5)
            <br>
        @endif
    @endforeach
@endif