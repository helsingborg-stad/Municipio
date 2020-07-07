<ul class="nav-accessibility nav-horizontal hidden-print rs_skip rs_preserve">
	@foreach (apply_filters('accessibility_items', array()) as $item) {{-- TODO: Move to controller. --}}
		<li>
			@icon([
				'icon' => $item['icon'],
				'size' => 'md'
			])
			@endicon
			@link([
				'href' => $item['href'],
				'attributeList' => ['onClick' => $item['script']]

			])
				{{$item['text']}}
			@endbutton
		</li>
	@endforeach

</ul>
