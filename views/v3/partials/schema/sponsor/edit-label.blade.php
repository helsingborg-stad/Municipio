@if(isset($sponsorTemplate['editUrls'][$labelKey]))
    <a href="{{ $sponsorTemplate['editUrls'][$labelKey] }}">{{ sprintf(__('Edit label: %s', 'municipio'), $sponsorTemplate['labels'][$labelKey]) }}</a>
@endif
