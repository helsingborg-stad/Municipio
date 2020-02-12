
<div class="{{$baseClass}}__top-title @if($avatar) {{$baseClass}}__with-avatar @endif
@if($dropdown && $dropdown['position'] === 'top')  {{$baseClass}}__with-top-dropdown @endif">
	@if($avatar)
		@avatar([
			'image' => $avatarImage,
			'name' => $avatarName
		])
		@endavatar
	@endif
	@if($href)
		<a class="{{$baseClass}}__link" href="{{$href}}">
			@typography([
				'variant' => "h3",
				'element' => "h3",
				'classList' => [$baseClass."__title"]
			])
			{{$title['text']}}
			@endtypography
		</a>
	@else
		@typography([
			'variant' => "h3",
			'element' => "h3",
			'classList' => [$baseClass."__title"]
		])
		{{$title['text']}}
		@endtypography
	@endif

	@if($byline['position'] === 'top')
		@typography([
			'variant' => "h4",
			'element' => "h4",
			'classList' => [$baseClass."__byline"]
		])
			{{$byline["text"]}}
		@endtypography
	@endif
	@if($dropdown && $dropdown['position'] === 'top')
		@include('Card.sub.card-dropdown')
	@endif
</div>