@element()
    @typography(['element' => 'h2'])
        {!! $lang->contactLabel !!}
    @endtypography
    @element(['classList' => ['o-grid', 'o-grid--half-gutter', 'u-margin__top--2']])
        @foreach ($personsAttributes as $personAttributes)
            @person(array_merge($personAttributes, ['classList' => ['o-grid-12@sm', 'o-grid-6@md']]))@endperson
        @endforeach
    @endelement
@endelement