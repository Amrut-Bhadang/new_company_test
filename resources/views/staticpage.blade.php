<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{$title}}</title>
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    </head>
    <style type="text/css">
        *{font-family: 'Lato', sans-serif;}
        .error_page h1 {
            font-size: 150px;
            line-height: 1;
            margin: 0;
        }
        .error_page {
            width: 400px;
            text-align: center;
            margin: 50px auto;
        }
        .error_page h4 {
            margin: 0;
            font-size: 23px;
        }
        .error_page a {
            background: #000;
            display: inline-block;
            color: #fff;
            text-decoration: none;
            padding: 15px 20px;
            margin-top: 10px;
        }
    </style>
    <body>
        <?php if ($content) { ?>
            {!!$content->description!!}
        <?php } else { ?>
            <div id="main">
                <div class="error_page">
                    <h1>404</h1>
                    <h4>Page not Found</h4>
                    <p>We're sorry, the page you requested could not be found. Please go back to the homepage.</p>
                    <a href="{{ url('/') }}">Go to Homepage</a>
                </div>
            </div>
        <?php } ?>
    </body>
</html>
