@if (isset($footerLayout['footers']) && is_array($footerLayout['footers']) && !empty($footerLayout['footer']))
    <footer id="footer" class="c-site-footer">
        @foreach ($footerLayout['headers'] as $footer)
            <div class="{{$footer['class']}}">
                @if (isset($footer['items']) && !empty($footer['items']))
                    <div class="{{$footer['rowClass']}}">

                        @foreach ($footer['items'] as $item)
                            <div class="{{$item['class']}}">
                                <?php dynamic_sidebar($item['id']); ?>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>
        @endforeach
    </footer>
@elseif($showAdminNotices === true)
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                @include('partials.notice',
                    ['notice' =>
                        ['class' => "info",
                        'icon' => "pricon pricon-info-o",
                        'text' => "You have not configured any footer. You can add a footer in the customizer."]
                    ]
                )
            </div>
        </div>
    </div>
@endif
