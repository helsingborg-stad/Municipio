@extends('templates.error')

@section('content')

<div class="container">
    <div class="grid">
        <div class="grid-lg-6 grid-md-8 grid-sm-12">
            <h1>404 <em>Sidan kunde inte hittas</em></h1>

            <ul class="actions">
                <li><a rel="nofollow" href="{{ home_url() }}?s={{ $keyword }}" class="link-item link-item-light">Sök efter <strong>"{{ $keyword }}"</strong> på Helsingborg.se</a></li>
                <li><a href="{{ home_url() }}" class="link-item link-item-light">Gå till Helsingborg.se</a></li>
            </ul>

            <p>
                Om du behöver ytterligare vägledning så kan du ringa till Helsingborg stads kontaktcenter på telefonnummer <a rel="nofollow" href="tel:042105000">042-10 50 00</a>
            </p>

            <p>
                <a href="javascript:history.go(-1);" class="btn btn-primary"><i class="fa fa-arrow-circle-o-left"></i> Gå tillbaka till föregående sida</a>
            </p>
        </div>
    </div>
</div>

@stop
