<html>
    <head>

        <title>The Subject of My Email</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                color: <?php echo $textColor; ?>;
                background-color: <?php echo $backgroundColor; ?>;
                margin: 0;
                padding: 0;
            }

            #header {
                background-color: <?php echo $headerBackgroundColor; ?>;
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
                background-color: <?php echo $backgroundColor; ?>;
            }

            #footer {
                background-color: <?php echo $footerBackgroundColor; ?>;
                padding: 30px;
            }

            #footer-content {
                font-size: 14px;
                margin: 0 auto;
                width: 550px;
                color: <?php echo $footerTextColor; ?>;
                line-height: 1.5;
            }

            #footer-content a {
                color: <?php echo $footerTextColor; ?>;
            }

        </style>

    </head>

    <body>
        <div id="header">
            <div id="header-content">
                <img src="<?php echo $logoSrc; ?>"/>
            </div>
        </div>

        <div id="content">

            <h1>
                The subject of this email
            </h1>