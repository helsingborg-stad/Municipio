@extends('templates.master')

@section('content')

<div class="container main-container">
    <div class="grid">
        <div class="grid-lg-9 grid-md-12">
            <div class="grid">
                <div class="grid-xs-12">
                    @if ($currentUser->ID === $user->ID)
                    <h1><?php _e('Your settings', 'municipio-intranet'); ?></h1>
                    @else
                    <h1><?php echo sprintf(__('Settings of %s', 'municipio-intranet'), $user->first_name . ' ' . $user->last_name) ; ?></h1>
                    @endif
                </div>
            </div>

            <div class="grid">
                <div class="grid-xs-12">
                    <form action="" method="post">
                        <div class="grid">
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label>E-postadress <small>(Går ej att ändra)</small></label>
                                    <input type="email" name="user_phone" value="{{ get_the_author_meta('email') }}" disabled>
                                </div>
                            </div>
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label>Telefonnummer</label>
                                    <input type="tel" name="user_phone">
                                </div>
                            </div>
                        </div>

                        <div class="grid">
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label>Förvaltning</label>
                                    <input type="text" name="user_administration">
                                </div>
                            </div>
                            <div class="grid-md-6">
                                <div class="form-group">
                                    <label>Avdelning</label>
                                    <input type="text" name="user_department">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <aside class="grid-lg-3 grid-md-12 sidebar-right-sidebar">
            <div class="grid">
                <div class="grid-xs-12">
                    <div class="box box-filled">
                        <h4 class="box-title">Redigera din profil</h4>
                        <div class="box-content">

                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

@stop
