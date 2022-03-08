<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        h1, h2, h3, h4, h5, p, span, a, li {
            font-family: "Hind", sans-serif;
            font-weight: 100;
        }
        p, span, li {
            color: #4c4c4c;
        }
        p, span, li, a {
            font-size: 16px;
        }
        body {
            margin: 0;
        }
        .content {
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            display: block;
            text-align: center;
            width: 60%;
            max-width: 90%;
            margin: auto;
            box-sizing: border-box;
        }
        h2 {
            font-weight: bold;
        }
        @media screen and (max-width: 640px) {
            .content {
                padding: 10px;
                background-color: #fff;
                border-radius: 10px;
                display: block;
                text-align: center;
                width: 90%;
                max-width: 90%;
                margin: auto;
            }
        }
        .container {
            background-color: #EBEFF2;
            width: 100%;
            padding-top: 9%;
            background-image: url('https://s3.amazonaws.com/boonb/prod/img/email-header.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: top;
        }
        footer {
            background-color: #1F2022;
            text-align: center;
            color: #fff;
            font-size: 16px;
            padding: 30px;
        }
        footer img {
            width:100%;
            max-width: 120px;
        }
        footer a {
            color: #fff !important;
            text-decoration: none;
        }
        footer p {
            margin: 5px;
            color: #fff !important;
        }
        footer span {
            color: #fff;
        }
        footer .logo{
            margin-bottom: 25px;
        }
        footer .networks {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        footer .social {
            margin: 10px;
        }
        footer .social img{
            height: 14px;
            width: inherit;
        }
        .text-center {
            text-align: center;
        }
    </style>

    @yield('assets')

    <link rel="stylesheet"
          href="https://use.fontawesome.com/releases/v5.8.2/css/all.css"
          integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay"
          crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Hind&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        @yield('content')
        <footer>
            <img src="https://boonb.s3.amazonaws.com/prod/img/boon-logo-white.png" class="logo">
            <p>&copy; {{ date('Y') }} Boon</p>
            <p>
                <a href="{{ url('/terms-of-service') }}">Terms and Conditions</a> |
                <a href="%unsubscribe_url%">Unsubscribe</a>
            </p>
            <div class="networks">
                <a href="https://www.facebook.com/doingboon/" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/fb-icon.png">
                </a>
                <a href="https://twitter.com/doingboon" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/tw-icon.png">
                </a>
                <a href="https://www.youtube.com/channel/UC_H8YxUhMe9ENvA_F7uwG8g" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/youtube-icon.png">
                </a>
                <a href="https://www.linkedin.com/company/doingboon" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/linked-icon.png">
                </a>
                <a href="https://www.instagram.com/doingboon/" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/in-icon.png">
                </a>
                <span>Download our mobile app</span>
                <!--<a href="https://apps.apple.com/us/app/doing-boon/id1471640517" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/android-icon.png">
                </a>-->
                <a href="https://apps.apple.com/us/app/doing-boon/id1471640517" class="social">
                    <img src="https://boonb.s3.amazonaws.com/prod/img/ios-icon.png">
                </a>
            </div>
        </footer>
    </div>
</body>
