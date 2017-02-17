<?php
    $icons = file_get_contents('http://hbgprime.dev/dist/pricons.json');
    $icons = json_decode($icons);
?>
<!doctype html>
<html class="no-js" lang="sv">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Test</title>

    <link rel="stylesheet" href="http://hbgprime.dev/dist/css/hbg-prime-icons.min.css">

    <style>
        body {
            font-size: 16px;
            font-family: 'arial';
        }

        .wrapper {
            padding: 20px;
        }

        .wrapper section > label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
        }

        select {
            display: block;
            width: 100%;
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px;
            font-size: 1em;
        }

        section + section {
            margin-top: 2em;
        }

        .icons {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .icons li {
            position: relative;
            margin: 0;
            padding: 0;
            display: inline-block;
            width: 40px;
            height: 40px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .icons li label {
            display: block;
            position: relative;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 40px;
            height: 40px;
        }

        .icons .pricon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .icons span {
            display: none;
        }

        .icons input[name="pricon-icon"] {
            display: none;
        }

        .icons input[name="pricon-icon"]:checked ~ label {
            color: #fff;
            border-color: #0073aa;
            background-color: #0085ba;
        }
    </style>

    <!--[if lt IE 9]>
    <script type="text/javascript">
        document.createElement('header');
        document.createElement('nav');
        document.createElement('section');
        document.createElement('article');
        document.createElement('aside');
        document.createElement('footer');
        document.createElement('hgroup');
    </script>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <![endif]-->
</head>
<body>
    <div class="wrapper">
        <section>
            <label>Select icon size</label>
            <select name="pricon-size">
                <option value="">Inherit</option>
                <option value="pricon-xs">X-Small</option>
                <option value="pricon-sm">Small</option>
                <option value="pricon-md">Medium</option>
                <option value="pricon-lg">Large</option>
                <option value="pricon-2x">2x</option>
                <option value="pricon-3x">3x</option>
                <option value="pricon-4x">4x</option>
                <option value="pricon-5x">5x</option>
            </select>
        </section>
        <section>
            <label>Select icon color</label>
            <input type="color" name="pricon-color" placeholder="Leave empty to inherit…">
        </section>
        <section>
            <label>Select icon</label>
            <ul class="icons">
                <?php foreach ($icons as $icon) : ?>
                <li>
                    <input id="<?php echo $icon->class; ?>" type="radio" name="pricon-icon" value="<?php echo $icon->name; ?>">
                    <label for="<?php echo $icon->class; ?>">
                        <i class="pricon pricon-lg <?php echo $icon->class; ?>"></i>
                        <span><?php echo $icon->name; ?></span>
                    </label>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </div>
</body>
</html>
