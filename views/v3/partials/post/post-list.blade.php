{{-- Deprecated in favor of the PostsList feature --}}
<div class="o-grid">
    <div class="o-grid-12">
        @table([
            'headings' => $posts['headings'],
            'list' => $posts['items'],
            'classList' => ['archive-list'],
            'context' => ['archive', 'archive.list', 'archive.list.list'],
        ])
        @endtable
    </div>
</div>