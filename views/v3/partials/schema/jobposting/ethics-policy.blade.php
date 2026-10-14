@foreach ($ethicsPolicies as $ethicsPolicy)
    @paper(['padding' => 4])
        {{$ethicsPolicy}}
    @endpaper
@endforeach
