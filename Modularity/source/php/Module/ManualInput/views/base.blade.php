@includeWhen(empty($hideTitle) && !empty($postTitle), 'partials.post-title')
@if (!empty($manualInputs))
    <div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : ' o-grid--half-gutter' }}">
        @foreach ($manualInputs as $input)
            @include('appearances.' . $input['view'])
        @endforeach
    </div>
@endif
