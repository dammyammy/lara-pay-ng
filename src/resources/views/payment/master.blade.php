<!doctype html>
<html lang="en-GB">
<head>
    <meta charset="UTF-8">
    <title>Payment Pages</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.4/paper/bootstrap.min.css"/>
    <style>
        .pad40{padding-top: 40px;}
    </style>
</head>
<body>
    <div class="continer">
        <div class="row">
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="https://github.com/dammyammy/lara-pay-ng">
                            &nbsp;&nbsp;<i class="fa fa-money"></i> Lara Pay NG
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        @yield('content')
        <div class="row">
            <hr/>

            <footer class="text-center">
                <p>Maintained by <i class="fa fa-github"></i>: <a href="https://github.com/dammyammy">@dammyammy</a></p>
                <p>Code licensed under <a rel="license" href="https://github.com/dammyammy/lara-pay-ng/blob/master/LICENSE" target="_blank">MIT</a>, documentation under <a rel="license" href="https://creativecommons.org/licenses/by/3.0/" target="_blank">CC BY 3.0</a>.</p>
                <ul class="list-inline list-unstyled text-muted">
                    <li>Currently: <b>dev-master</b></li>
                    <li>·</li>
                    <li><a href="https://github.com/dammyammy/lara-pay-ng">GitHub</a></li>
                    <li>·</li>
                    <li><a href="https://github.com/dammyammy/lara-pay-ng/blob/master/docs">Docs</a></li>
                    <li>·</li>
                    <li><a href="https://github.com/dammyammy/lara-pay-ng/issues">Issues</a></li>

                </ul>
            </footer>
        </div>

    </div>

</body>
</html>

