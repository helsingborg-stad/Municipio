@foreach($contacts as $contact)
    @collection(['unbox' => true, 'bordered' => true])
        @if($contact['name'] || $contact['role'])
            @collection__item(['icon' => 'person', 'classList' => ['u-align-items--center']])
                @typography(['element' => 'p'])
                    @if($contact['name']) <strong>{{ $contact['name'] }}</strong> @endif
                    @if($contact['name'] && $contact['role'])<br>@endif
                    {{ $contact['role'] }}
                @endtypography
            @endcollection__item
        @endif
        @if($contact['email'])
            @collection__item(['icon' => 'mail_outline', 'link' => 'mailto:' . $contact['email'], 'classList' => ['u-align-items--center']])
                @typography(['variant' => 'email']){{ $contact['email'] }}@endtypography
            @endcollection__item
        @endif
        @if($contact['telephone'])
            @collection__item(['icon' => 'phone', 'link' => $contact['telephoneHref'] ? 'tel:' . $contact['telephoneHref'] : false, 'classList' => ['u-align-items--center']])
                @typography(){{ $contact['telephone'] }}@endtypography
            @endcollection__item
        @endif
    @endcollection
@endforeach
