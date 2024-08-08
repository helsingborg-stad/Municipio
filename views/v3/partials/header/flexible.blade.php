@extends('templates.header', ['classList' => ['c-header', 'c-header--flexible']])

@section('primary-navigation')
    @if (!empty($headerData))
        <div class="o-container grid-container">
                <div class="c-header__logotype-area">
                    @if(!empty($headerData['logotypeItems']))
                        @foreach($headerData['logotypeItems'] as $name => $data)
                            <div class="{{implode(' ', $data)}}">
                                @includeIf('partials.header.components.' . $name)
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="c-header__main-upper-area">            
                    @if(!empty($headerData['mainUpperItems']))
                        @foreach($headerData['mainUpperItems'] as $name => $data)
                            <div class="{{implode(' ', $data)}}">
                                @includeIf('partials.header.components.' . $name)
                            </div>
                        @endforeach
                    @endif
                </div>
                <div class="c-header__main-lower-area">
                    @if(!empty($headerData['mainLowerItems']))
                        @foreach($headerData['mainLowerItems'] as $name => $data)
                        <div class="{{implode(' ', $data)}}">
                            @includeIf('partials.header.components.' . $name)
                        </div>
                        @endforeach
                    @endif
                </div>
        </div>
        
        @if(!empty($megaMenuItems) && (isset($headerData['mainLowerItems']['mega-menu']) || isset($headerData['mainUpperItems']['mega-menu'])))
            @include('partials.navigation.megamenu')
        @endif
        @if (isset($headerData['mainLowerItems']['search-modal']) || isset($headerData['mainUpperItems']['search-modal']))
            @include('partials.search.search-modal')
        @endif
    @endif
@endsection
