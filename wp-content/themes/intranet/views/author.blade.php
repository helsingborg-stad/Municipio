@extends('templates.master')

@section('content')

<header class="profile-header">
    <div class="profile-header-background" style="background-image:url('https://scontent-arn2-1.xx.fbcdn.net/v/t1.0-1/c0.44.160.160/p160x160/562742_10151832711869931_1531739462_n.jpg?oh=fe481388488ff1ccf21e0ee1099b8ff6&oe=57A0A2A5');"></div>

    <div class="container">
        <div class="grid">
            <div class="grid-xs-12">
                <div class="profile-header-content">
                    <div class="profile-image" style="background-image:url('https://scontent-arn2-1.xx.fbcdn.net/v/t1.0-1/c0.44.160.160/p160x160/562742_10151832711869931_1531739462_n.jpg?oh=fe481388488ff1ccf21e0ee1099b8ff6&oe=57A0A2A5');"></div>
                    <div class="profile-basics">
                        <h1 class="profile-fullname">{{ get_the_author_meta('first_name') . ' ' . get_the_author_meta('last_name') }}</h1>
                        <span class="profile-title">Konsult Lexicon, webbutvecklare</span>
                        <span class="profile-department">Stadsledningsförvaltningen, Webbenheten</span>
                    </div>

                    @if (get_current_user_id() == get_the_author_meta('ID') || is_super_admin())
                    <ul class="profile-actions">
                        <li><a href="{{ home_url('user/' . get_the_author_meta('user_login') . '/edit') }}" class="btn btn-primary"><i class="fa fa-wrench"></i> Redigera profil</a></li>
                    </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container main-container">
    <div class="grid">
        <div class="grid-md-8">
            <article>
                <h2>Om mig</h2>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque ultricies aliquam dolor et tristique.
                    Aenean nec velit vel sapien scelerisque luctus in quis erat. In lacinia massa vitae congue scelerisque.
                    Phasellus ultricies vehicula ultrices. Maecenas a velit ligula. Maecenas vitae massa eget mi dapibus fermentum.
                    In nec magna eros. Fusce nec semper libero, bibendum rhoncus dui. Mauris a ante eget felis porttitor aliquam id in orci.
                </p>

                <p>
                    Fusce eget augue eget felis facilisis aliquam quis id odio. Aenean aliquam consectetur ipsum quis lobortis.
                    Proin finibus a sem ac tincidunt. Cras sed imperdiet elit. Integer accumsan purus ut eros consectetur, nec congue quam posuere.
                    Cras hendrerit risus odio, porta malesuada nibh elementum vel. Praesent commodo ex in congue tristique.
                </p>
            </article>
        </div>

        <div class="grid-md-4">
            <div class="grid">
                <div class="grid-xs-12">
                    <div class="gutter gutter-bottom">
                        <div class="notice warning"><i class="fa fa-warning"></i> {{ get_the_author_meta('first_name') }} är pappaledig till 2016-06-14</div>
                    </div>
                </div>
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title">Kontaktinformation</h4>
                        <div class="box-content">
                            <p>
                                <strong>E-postadress</strong><br>
                                <a href="mailto:{{ get_the_author_meta('email') }}">{{ get_the_author_meta('email') }}</a>
                            </p>
                            <p>
                                <strong>Telefonnummer</strong><br>
                                <a href="tel:0704426420">070 - 442 64 20</a>
                            </p>
                            <p>
                                <strong>Kontor</strong><br>
                                Kontaktcenter
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop
