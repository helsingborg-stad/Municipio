@foreach ($post->getSchemaProperty('hiringOrganization') ?? [] as $organization)
    @if ($organization['ethicsPolicy'] ?? null)
        @paper(['padding' => 4])
            {{$organization['ethicsPolicy']}}
        @endpaper
    @endif
@endforeach
