<html>
    <head>

        <title>The Subject of My Email</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: {{ $styles['textColor'] }};
                background-color: {{ $styles['backgroundColor'] }};
                margin: 0;
                padding: 0;
            }

            #header {
                background-color: {{ $styles['headerBackgroundColor'] }};
                padding: 30px;
            }

            #header-content {
                margin: 0 auto;
                width: 550px;
            }

            #header-content img {
                max-width: 200px;
            }

            #content {
                padding: 40px 30px;
                margin: 0 auto;
                width: 550px;
                background-color: {{ $styles['backgroundColor'] }};
            }

            #footer {
                background-color: {{ $styles['footerBackgroundColor'] }};
                padding: 30px;
            }

            #footer-content {
                font-size: 14px;
                margin: 0 auto;
                width: 550px;
                color: {{ $styles['footerTextColor'] }};
                line-height: 1.5;
            }

            #footer-content a {
                color: {{ $styles['footerTextColor'] }};
            }

        </style>

    </head>

    <body>
        <div id="header">
            <div id="header-content">
                <img src="{{ $logoSrc }}"/>
            </div>
        </div>

        <div id="content">

            <h1>{{ $subject }}</h1>

            {!! $content !!}

        </div>
        
        <div id="footer">
            <div id="footer-content">
                <p>{!! $footerText !!}</p>
            </div>
        </div>
    </body>
</html>