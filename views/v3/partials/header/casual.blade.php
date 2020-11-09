@extends('templates.header', ['classList' => ['c-header c-header--casual']])

@section('primary-navigation')
    @if (!empty($primaryMenuItems))
        @navbar([
            'logo'      => $logotype->standard['url'],
            'items'     => $primaryMenuItems,
            'sidebar'   => ['trigger' => "js-mobile-sidebar"]
        ])

        @endnavbar
    @endif
@stop