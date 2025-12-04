@section('layout')
    <div class="o-container">

        {{-- Helper navigation --}}
        @hasSection('helper-navigation')
            <div class="o-grid o-grid--no-margin u-print-display--none">
                <div class="o-grid-12">
                    @yield('helper-navigation')
                </div>
            </div>
        @endif

        {{-- Above columns sidebar --}}
        @hasSection('above')
            <div class="o-grid u-print-display--none">
                <div class="o-grid-12">
                    @yield('above')
                </div>
            </div>
        @endif

        <!--  Main content padder -->
        <div class="u-padding__x--{{ $mainContentPadding['md'] }}@lg u-padding__x--{{ $mainContentPadding['lg'] }}@lg u-padding__x--{{ $mainContentPadding['lg'] }}@xl u-margin__bottom--12">
            <div class="o-grid o-grid--nowrap@lg o-grid--nowrap@xl">

                @hasSection('sidebar-left')
                    <div
                        class="o-grid-12 o-grid-{{ $leftColumnSize }}@lg o-grid-{{ $leftColumnSize }}@xl o-order-2 o-order-1@lg o-order-1@xl u-print-display--none">
                        @yield('sidebar-left')
                    </div>
                @endif

                <div
                    class="o-grid-12 o-grid-auto@lg o-grid-auto@xl o-order-1 o-order-2@lg o-order-2@xl u-display--flex u-flex--gridgap  u-flex-direction--column">
                    @yield('content')
                    @yield('content.below')
                </div>

                @hasSection('sidebar-right')
                    <div
                        class="o-grid-12 o-grid-{{ $rightColumnSize }}@lg o-grid-{{ $rightColumnSize }}@xl o-order-3 o-order-3@lg o-order-3@xl u-print-display--none">
                        @yield('sidebar-right')
                    </div>
                @endif
            </div>
        </div>

        @hasSection('below')
            <div class="o-grid">
                <div class="o-grid-12">
                    @yield('below')
                </div>
            </div>
        @endif
    </div>
@show