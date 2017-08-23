<header id="site-header" class="site-header @if(isset($headerLayout['class'])) {{ $headerLayout['class'] }} @endif pos-relative">
    <div class="stripe stripe-md hidden-xs hidden-sm">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>

    <div class="print-only container">
        <div class="grid">
            <div class="grid-sm-12">
                {!! municipio_get_logotype('standard') !!}
            </div>
        </div>
    </div>

    @include('partials.header.' . $headerLayout['template'])
</header>

@include('partials.user.modal-login')
@include('partials.user.modal-password')
