@if (isset($footerSections) && is_array($footerSections) && !empty($footerSections))
    <footer class="s-site-footer">
        @foreach ($footerSections as $footer)
            <div {!! $footer->attributes->outputAttributes() !!}>
                <div class="container">
                    <div class="grid">
                        @foreach ($footer->columns as $column)
                            @if (is_active_sidebar($column['sidebar']))
                                <div {!! $column['attributes']->outputAttributes() !!}>
                                    <?php dynamic_sidebar($column['sidebar']); ?>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </footer>
@elseif($showAdminNotices === true)
    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                @component('components.notice', ['type' => 'info'])
                    @slot('icon')
                        @include('utilities.icon', ['id' => 'notice-info'])
                    @endslot
                    You have not configured any footer. You can add a footer in the customizer.
                @endcomponent
            </div>
        </div>
    </div>
@endif
