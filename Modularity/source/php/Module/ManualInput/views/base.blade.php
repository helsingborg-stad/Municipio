@includeWhen(empty($hideTitle) && !empty($postTitle), 'partials.post-title')
@if (!empty($manualInputs))
    <div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }} u-margin__top--4">
        @foreach ($manualInputs as $input)
            @include('appearances.' . $input['view'])
        @endforeach
    </div>
@endif
