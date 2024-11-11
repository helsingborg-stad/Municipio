<style type="text/css">
    /*<![CDATA[*/
    @if(!empty($fonts['heading']['src']))
            @font-face {
                font-family: {!! $fonts['heading']['font-family'] !!};
                font-weight: {!! $fonts['heading']['variant']!!};
                src: url({!!$fonts['heading']['src']!!}) format('truetype');
            } 
    @endif

    @if(!empty($fonts['base']['src']))
            @font-face {
                font-family: {!! $fonts['base']['font-family'] !!};
                font-weight: {!! $fonts['base']['variant']!!};
                src: url({!!$fonts['heading']['src']!!}) format('truetype');
            }
    @endif

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        font-family: {!!$fonts['heading']['font-family']!!}, sans-serif;
        font-weight: {!! $fonts['heading']['variant']!!} !important;
        line-height: 0.9;
        margin-top: 0;
    }

    article,
    section,
    p,
    .pdf-frontpage__introduction {
        font-family: {!!$fonts['base']['font-family']!!}, sans-serif;
        font-weight: {!! $fonts['base']['variant']!!};
    }

    section .pdf-toc__item {
        font-family: sans-serif;
        font-weight: 400;
    }

    .lead {
        font-weight: bold !important;
    }

    h1,
    .pdf-heading--h1 {
        font-size: {!! !empty($styles['typography_h1']['font-size']) ? $styles['typography_h1']['font-size'] : '48px' !!};
        line-height: 0.9;
    }

    h2 {
        font-size: {!! !empty($styles['typography_h2']['font-size']) ? $styles['typography_h2']['font-size'] : '32px' !!};
    }

    h3 {
        font-size: {!! !empty($styles['typography_h3']['font-size']) ? $styles['typography_h3']['font-size'] : '24px' !!};
    }

    h4 {
        font-size: {!! !empty($styles['typography_h4']['font-size']) ? $styles['typography_h4']['font-size'] : '20px' !!};
    }

    h5 {
        font-size: {!! !empty($styles['typography_h5']['font-size']) ? $styles['typography_h5']['font-size'] : '18px' !!};
    }

    h6 {
        font-size: {!! !empty($styles['typography_h6']['font-size']) ? $styles['typography_h6']['font-size'] : '16px' !!};
    }
    /*]]>*/
</style>
