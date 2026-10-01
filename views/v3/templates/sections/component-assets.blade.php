@php($municipioComponentAssets = \Municipio\Styleguide\ComponentAssets\WordPressAssetEnqueuer::instance())

@if ($municipioComponentAssets)
    @push('styles')
        {!! $municipioComponentAssets->renderStyles($__env->yieldContent('body-content')) !!}
    @endpush

    @push('scripts')
        {!! $municipioComponentAssets->renderScripts() !!}
    @endpush
@endif
