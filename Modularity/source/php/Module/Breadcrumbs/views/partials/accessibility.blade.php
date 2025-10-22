@if (!empty($accessibilityItems) && is_array($accessibilityItems))
    <ul class="nav-accessibility nav-horizontal u-print-display--none unlist u-display--none@xs u-display--none@sm u-print-display--none"
        id="accessibility-items" role="menubar">
        @foreach ($accessibilityItems as $item)
            <li role="menuitem">
                @link([
                    'href' => $item['href'] ?? null,
                    'attributeList' => [
                        'onClick' => $item['script'] ?? '',
                        'aria-label' => $item['label'] ?? '',
                    ],
                ])
                @icon([
                    'icon' => $item['icon'],
                    'size' => $item['size'] ?? 'md',
                    'filled' => $item['filled'] ?? true,
                    'attributeList' => $item['attributeList'] ?? [],
                    'classList' => $item['classList'] ?? []
                ])
                @endicon
                {{ $item['text'] }}
                @endbutton
            </li>
        @endforeach
    </ul>
@endif
