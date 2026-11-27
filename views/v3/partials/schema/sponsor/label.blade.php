@if(isset($sponsorTemplate['editUrls'][$labelKey]))
    <a href="{{ $sponsorTemplate['editUrls'][$labelKey] }}">{{ $sponsorTemplate['labels'][$labelKey] }}</a>
@else
    {{ $sponsorTemplate['labels'][$labelKey] }}
@endif
