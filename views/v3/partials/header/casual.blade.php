@extends('templates.header', ['classnames' => ['c-header c-header--casual']])

@section('primary-navigation')
    @if (!empty($primaryMenuItems))
        <div style="overflow-x: hidden; overflow-y: visible;">
            @navbar([
                'logo'      => $logotype->standard['url'],
                'items'     => $primaryMenuItems,
                'sidebar'   => ['trigger' => "js-mobile-sidebar"]
            ])

            @endnavbar
        </div>
    @endif
@stop