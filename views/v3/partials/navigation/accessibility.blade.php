@if (!empty($accessibilityMenu['items']) && is_array($accessibilityMenu['items']))
<ul class="nav-accessibility nav-horizontal u-print-display--none unlist u-print-display--none"
id="accessibility-items" role="menubar">
    @foreach ($accessibilityMenu['items'] as $item)
        @if (!empty($item['dropdown']))
        @dropdown([
            'popup' => 'click',
            'componentElement' => 'li'
            ])
            @link([
                'href' => null,
                'attributeList' => [
                    'aria-label' => $item['label'] ?? '',
                ],
                'classList' => [
                    'js-dropdown-button'
                ]
            ])
           
            @icon([
                'icon' => $item['button']['icon'],
                'size' => 'md',
            ])
            @endicon
            {{ $item['button']['text'] ?? __('Expand', 'municipio') }}
            @endlink
                @slot('list')
                    @foreach($item['dropdown'] as $dropdownItem)
                        @include('partials.navigation.accessibility-item', [
                            'item' => $dropdownItem
                        ])
                    @endforeach
                @endslot
            @enddropdown
        @else
        @include('partials.navigation.accessibility-item', [
            'item' => $item
        ])
        @endif
    @endforeach
</ul>
@endif
