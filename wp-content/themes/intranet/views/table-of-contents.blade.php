@extends('templates.master')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-xs-12">
            <h1><?php _e('A-Z', 'municipio-intranet'); ?></h1>

            <div class="grid">
                <div class="grid-xs-12">
                    <form action="" method="get" class="form-horizontal-block">
                        <div class="grid">
                            <div class="grid-md-5">
                                <div class="form-group">
                                    <label for="table-of-contents-department"><?php _e('Department', 'municipio-intranet'); ?></label>
                                    <select id="table-of-contents-department" name="department">
                                        <option value=""><?php _e('All', 'municipio-intranet'); ?></option>
                                        @foreach (\Intranet\Helper\Multisite::getSitesList(true) as $site)
                                            <option value="{{ $site->blog_id }}" {{ selected($selectedDepartment, $site->blog_id) }}>{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="grid-md-5">
                                <div class="form-group">
                                    <label for="table-of-contents-text-search"><?php _e('Text search', 'municipio-intranet'); ?></label>
                                    <input name="title" id="table-of-contents-text-search" type="search" placeholder="Sök i listan…" value="{{ $titleQuery }}">
                                </div>
                            </div>
                            <div class="grid-md-2">
                                <input class="btn btn-primary btn-block" type="submit" value="<?php _e('Search'); ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <div class="form-group">
                        <label>Hoppa till bokstav</label>
                        <ul class="pagination pagination-lg">
                        @foreach ($tableOfContents as $key => $pages)
                             <li><a href="#index-{{ $key }}">{{ strtoupper($key) }}</a></li>
                        @endforeach
                        @foreach ($tableOfContents as $key => $pages)
                             <li><a href="#index-{{ $key }}">{{ strtoupper($key) }}</a></li>
                        @endforeach
                        @foreach ($tableOfContents as $key => $pages)
                             <li><a href="#index-{{ $key }}">{{ strtoupper($key) }}</a></li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid">
                @foreach ($tableOfContents as $key => $pages)
                <div class="grid-md-12">
                    <div class="box box-panel box-panel-secondary" id="index-{{ $key }}">
                        <h2 class="box-title">{{ strtoupper($key) }}</h2>
                        <ul class="table-of-contents">
                            @foreach ($pages as $page)
                            <li class="clearfix">
                                <a href="{{ get_blog_permalink($page->blog_id, $page->ID) }}" class="link-item pull-left">{{ $page->post_title }}</a>
                                <span class="network-title label label-sm label-creamy">{!! municipio_intranet_format_site_name(\Intranet\Helper\Multisite::getSite($page->blog_id)) !!}</span>
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
