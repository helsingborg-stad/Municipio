<div class="{{ $classes }} script-{{$scriptWrapWithClassName}}">
    @if (!$hideTitle && !empty($postTitle))
        <div class="script-{{$scriptWrapWithClassName}}__header">
            @php
                $wrappingClass = 'script-'.$scriptWrapWithClassName.'-title';
            @endphp
            @typography([
                'element'   => 'h2',
                'variant'   => 'h4',
                'classList' => [$wrappingClass]
            ])
                {!! $postTitle !!}
            @endtypography
        </div>
    @endif
	
	@include('partials.content')

    @image([
            'src'=> $placeholder['url'],
            'alt' => $placeholder['alt'],
            'classList' => ['box-image','u-print-display--inline-block','u-display--none']
        ])
    @endimage
</div>
