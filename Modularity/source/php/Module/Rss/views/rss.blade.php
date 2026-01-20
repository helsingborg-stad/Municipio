<div class="{{ $classes }}">
    @if (!$hideTitle && !empty($postTitle))

			@typography([
				'element' => 'h2', 
				'variant' => 'h2', 
				'classList' => ['module-title']
			])
				{!! $postTitle !!}
			@endtypography

    @endif

	<ul>
	@if(! isset($items['error']))
		@foreach($items as $item)
			<li>
				@if(\Modularity\Module\Rss\Rss::getRssLink($item->get_link()))

					@link([
						'href' =>  \Modularity\Module\Rss\Rss::getRssLink($item->get_link())
					])
						@typography([
							'element' => "span",
							'classList' => ['link-item','title']
						])
							{!! \Modularity\Module\Rss\Rss::getRssTitle($item->get_title()) !!}
						@endtypography

						@if($date && $item->get_date('U'))

							@typography([
								'element' => "time",
								'classList' => ['date','text-sm','text-dark-gray']
							])
								{!!  date_i18n(get_option('date_format'), $item->get_date('U')) !!}
							@endtypography

						@endif

					@endlink
				@else

					@typography([
							'element' => "span",
							'classList' => ['link-item','title']
					])
							{!! \Modularity\Module\Rss\Rss::getRssTitle($item->get_title()) !!}
					@endtypography

					@if($date && $item->get_date('U'))

						@typography([
								'element' => "time",
								'classList' => ['date','text-sm','text-dark-gray']
						])
								{!!  date_i18n(get_option('date_format'), $item->get_date('U')) !!}
						@endtypography

					@endif
				@endif

				@if($summary && \Modularity\Module\Rss\Rss::getRssSummary($item->get_description()))

					@typography([
								'element' => "p"
						])
								{!! \Modularity\Module\Rss\Rss::getRssSummary($item->get_description()) !!}
					@endtypography

				@endif

				@if($author && \Modularity\Module\Rss\Rss::getRssAuthor($item->get_author()))
					@typography([
								'element' => "p"
						])
								{!! \Modularity\Module\Rss\Rss::getRssAuthor($item->get_author()) !!}
					@endtypography

				@endif
            </li>
		@endforeach
	@else
		<li class="notice warning">
			@icon(['icon' => 'warning', 'size' => 'sm']) @endicon {{ $items['error'] }}
		</li>
	@endif
	</ul>
</div>
