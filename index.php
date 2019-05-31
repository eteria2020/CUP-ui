<!DOCTYPE html>
<?php $username = $_SERVER['PHP_AUTH_USER']; ?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Service Pages Index</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css"
         integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css
        " integrity="sha384-aUGj/X2zp5rLCbBxumKTCw2Z50WgIr1vs/PFN4praOTvYXWlVyh2UtNUU0KAUhAX" crossorigin="anonymous">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        <?php if ($username=="admin") { ?>

        <!-- Access allowed only for admins -->
        <div class="container theme-showcase" role="main">
            <div class="jumbotron text-center">
                <h1>Service Page Index</h1>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title text-center">Various Pages</h3>
                </div>
                <div class="panel-body">
                    <div class="list-group">
						<a href="http://admin.sharengo.it" class="list-group-item">
                            <h4 class="list-group-item-heading">Area Admin</h4>
                            <p class="list-group-item-text">http://admin.sharengo.it</p>
                        </a>
                        <a href="http://core.sharengo.it/ui/auto.php" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina Comandi</h4>
                              <p class="list-group-item-text">http://core.sharengo.it/ui/auto.php</p>
                        </a>
						<a href="http://core.sharengo.it/ui/reports/trips.php" class="list-group-item">
                            <h4 class="list-group-item-heading">Statistiche</h4>
                              <p class="list-group-item-text">http://core.sharengo.it/ui/reports/trips.php</p>
                        </a>
                        <a href="http://core.sharengo.it/ui/cars_info.php" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina Car-Info</h4>
                            <p class="list-group-item-text">http://core.sharengo.it/ui/cars_info.php</p>
                        </a>
                        <a href="http://core.sharengo.it/ui/opentrips.php" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina corse aperte</h4>
                            <p class="list-group-item-text">http://core.sharengo.it/ui/opentrips.php</p>
                        </a>
                        <a href="http://core.sharengo.it/system/system.html" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina di sistema</h4>
                            <p class="list-group-item-text">http://core.sharengo.it/system/system.html</p>
                        </a>
                        <a href="http://core.sharengo.it:9000/#/hosts/localhost" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina ON/OFF Servizi</h4>
                            <p class="list-group-item-text">http://core.sharengo.it:9000/#/hosts/localhost</p>
                        </a>
                        <a href="http://core.sharengo.it:7602/failed" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina coda servizi</h4>
                            <p class="list-group-item-text">http://core.sharengo.it:7602/failed</p>
                        </a>
                        <a href=" http://admin.sharengo.it/user/login" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina ADMIN</h4>
                            <p class="list-group-item-text"> http://admin.sharengo.it/user/login</p>
                        </a>
                        <a href="http://admin.sharengo.it/call-center" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina Call-Center</h4>
                            <p class="list-group-item-text">http://admin.sharengo.it/call-center</p>
                        </a>
						<a href="http://issues.omniaevo.it" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina Ticket</h4>
                            <p class="list-group-item-text">http://issues.omniaevo.it</p>
                        </a>
                        <a href="http://dev.omniaevo.it" class="list-group-item">
                            <h4 class="list-group-item-heading">Pagina RedMine</h4>
                            <p class="list-group-item-text">http://dev.omniaevo.it</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php }else{ ?>
            <div class="container theme-showcase" role="main">
                <div class="jumbotron text-center">
                    <h1>Service Page</h1>
                </div>
                <div class="alert alert-danger" role="alert">
                    <strong>Warning!</strong> You are not allowed to see this page.
                </div>
            </div>
        <?php } ?>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
        
        <script>
            $('a').click(function() {
                $(this).attr('target', '_blank');
            });
       </script>
  </body>
</html>