@extends('partials.footer')

@section('footer-body')
    @if (isset($footerSections) && is_array($footerSections) && !empty($footerSections))
            @foreach ($footerSections as $footer)
                <div {!! $footer->wrapper !!}>
                    <div {!! $footer->container !!}>
                        <div {!! $footer->grid !!}>
                            @foreach ($footer->sidebars as $sidebar)
                                @if (is_active_sidebar($sidebar['id']))
                                    <div {!! $sidebar['attributes'] !!}>
                                        <?php dynamic_sidebar($sidebar['id']); ?>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
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
@stop
