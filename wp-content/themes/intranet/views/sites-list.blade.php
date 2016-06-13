@extends('templates.master')

@section('content')

<div class="container main-container">

    <div class="grid">
        @foreach (\Intranet\Helper\Multisite::getSitesList(true) as $site)
            <div class="grid-md-4">
                <a href="{{ $site['path'] }}" class="box box-index">
                    <div class="box-content">
                        <h5 class="box-index-title link-item">{!! municipio_intranet_format_site_name($site) !!}</h5>
                        @if (!empty($site['description']))
                        <p>{{ $site['description'] }}</p>
                        @endif
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>

@stop
