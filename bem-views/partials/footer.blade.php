@if (isset($footerLayout['sidebars']) && is_array($footerLayout['sidebars']) && !empty($footerLayout['sidebars']))
    <footer id="footer" class="c-footer c-footer--customizer {{$footerLayout['size']}}">
        <div class="container">
            <div class="grid">
                @foreach ($footerLayout['sidebars'] as $sidebar)
                    <div {!! $sidebar->getAttributes() !!}>
                        <?php dynamic_sidebar($sidebar->getSidebar()); ?>
                    </div>
                @endforeach
            </div>
        </div>
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
