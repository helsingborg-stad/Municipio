@extends('templates.master')

@section('content')

<section class="creamy creamy-border-bottom gutter-xl gutter-vertical">
    <div class="container">
        <div class="grid">
            <div class="grid-lg-6">
                <div class="box box-panel">
                    <h4 class="box-title">Genvägar</h4>
                    <ul>
                        <li><a href="#" class="link-item">Självservice</a></li>
                        <li><a href="#" class="link-item">Skolornas läsårstider</a></li>
                        <li><a href="#" class="link-item">Felanmälan</a></li>
                        <li><a href="#" class="link-item">Allt kommunalt på en karta</a></li>
                        <li><a href="#" class="link-item">Lediga jobb</a></li>
                        <li><a href="#" class="link-item">Här kan du parkera</a></li>
                        <li><a href="#" class="link-item">Tyck till om Helsingborgs stad</a></li>
                    </ul>
                </div>
            </div>
            <div class="grid-lg-6">
                <div class="box box-panel">
                    <h4 class="box-title">Evenemang</h4>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td width="80" class="text-center text-sm">
                                    <strong>Idag</strong><br>
                                    11:00
                                </td>
                                <td>
                                    <a href="#" class="link-item">Vinterresa</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center text-sm">
                                    <strong>Idag</strong><br>
                                    11:00
                                </td>
                                <td>
                                    <a href="#" class="link-item">Vinterresa</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center text-sm">
                                    <strong>Idag</strong><br>
                                    11:00
                                </td>
                                <td>
                                    <a href="#" class="link-item">Vinterresa</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-center text-sm">
                                    <strong>Idag</strong><br>
                                    11:00
                                </td>
                                <td>
                                    <a href="#" class="link-item">Vinterresa</a>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                    <a href="#" class="link-item">Visa fler evenemang</a>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container gutter-xl gutter-vertical">
    <div class="grid">
        <div class="grid-lg-12">
            <h1 class="text-highlight">Aktuellt i Helsingborgs stad</h1>
        </div>
    </div>

    <div class="grid">
        <div class="grid-md-4 grid-sm-6">
            <a href="#" class="box box-news">
                <img src="http://www.helsingborg.se/wp-content/uploads/2014/12/Kommunalanstalld_420x280.jpg" alt="Kommunalanställd">
                <div class="box-content">
                    <h5 class="link-item link-item-light">Ledia jobb i Helsingborgs stad</h5>
                </div>
            </a>
        </div>
        <div class="grid-md-4 grid-sm-6">
            <a href="#" class="box box-news">
                <img src="http://www.helsingborg.se/wp-content/uploads/2014/12/Kommunalanstalld_420x280.jpg" alt="Kommunalanställd">
                <div class="box-content">
                    <h5 class="link-item link-item-light">Ledia jobb i Helsingborgs stad</h5>
                </div>
            </a>
        </div>
        <div class="grid-md-4 grid-sm-6">
            <a href="#" class="box box-news">
                <img src="http://www.helsingborg.se/wp-content/uploads/2014/12/Kommunalanstalld_420x280.jpg" alt="Kommunalanställd">
                <div class="box-content">
                    <h5 class="link-item link-item-light">Ledia jobb i Helsingborgs stad</h5>
                </div>
            </a>
        </div>
    </div>

    <div class="grid">
        <div class="grid-md-6">
            <div class="box box-panel box-panel-secondary">
                <h4 class="box-title"></h4>
                <ul>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                </ul>
            </div>
        </div>
        <div class="grid-md-6">
            <div class="box box-panel box-panel-secondary">
                <h4 class="box-title"></h4>
                <ul>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                    <li><a href="#" class="link-item">Nyhet</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@stop
