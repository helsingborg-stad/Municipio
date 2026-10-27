@foreach($requirements as $requirement)
    <div class="c-stack">
        @if($requirement['name']) @typography(['element' => 'p'])<strong>{{ $requirement['name'] }}</strong>@endtypography @endif
        @if($requirement['description']) <div>{!! $requirement['description'] !!}</div> @endif
        @if($requirement['counterPerformance'])
            <div>
                @typography(['element' => 'p'])<strong>{{ __('Counter-performance', 'municipio') }}</strong>@endtypography
                @foreach($requirement['counterPerformance'] as $proposal)
                    @typography(['element' => 'p']){{ $proposal }}@endtypography
                @endforeach
            </div>
        @endif
    </div>
@endforeach
