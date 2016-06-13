@extends('templates.master')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-xs-12">
            <h1>A-Ö</h1>

            <div class="grid">
                <div class="grid-xs-12">
                    <ul class="pagination pagination-lg">
                    @foreach ($tableOfContents as $key => $pages)
                         <li><a href="#index-{{ $key }}">{{ strtoupper($key) }}</a></li>
                    @endforeach;
                    </ul>
                </div>
            </div>

            <div class="grid">
                @foreach ($tableOfContents as $key => $pages)
                <div class="grid-md-12">
                    <div class="box box-panel box-panel-secondary" id="index-{{ $key }}">
                        <h2 class="box-title">{{ strtoupper($key) }}</h2>
                        <ul>
                            @foreach ($pages as $page)
                            <li class="clearfix">
                                <a href="{{ get_blog_permalink($page->blog_id, $page->ID) }}" class="link-item pull-left">{{ $page->post_title }}</a>
                                <span class="network-title label label-sm label-creamy pull-right">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($page->blog_id)) !!}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@stop
