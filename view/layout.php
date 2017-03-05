<!DOCTYPE html>
<html>
<head>


    <title>Google drive manager</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1 maximum-scale=1.0, user-scalable=yes" />
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="view/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="view/css/ui.fancytree.css" />
    <link rel="stylesheet" type="text/css" href="view/css/gdrive.css" />


    <script type="text/javascript" src="view/js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript" src="view/js/jquery-ui.min.js"></script>
    <script type="text/javascript" src="view/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="view/js/jquery.fancytree.js"></script>
    <script type="text/javascript" src="view/js/jquery.fancytree.table.js"></script>
    <script type="text/javascript" src="view/js/jquery.fancytree.glyph.js"></script>
    <script type="text/javascript" src="view/js/jquery.fancytree.wide.js"></script>


    <script type="text/javascript" src="view/js/gdrive.js"></script>

    <script>
        window.gtoken = '<?php echo $_SESSION['_token']['access_token']?:''; ?>';


    </script>

</head>
<body>

<noscript><div id="no-script">This apps requires JavaScript, please enable JavaScript on your browser.</div></noscript>

<!-- Begin page content -->
<div class="container">
    <div class="page-header">
        <h1>Google Drive Manager</h1>
    </div>

<?php

// here include main view via hidden var $viewfile

 include $viewfile;

?>

</div>

<footer class="footer">
    <div class="container">
        <p class="text-muted">2017 Unima </p>
    </div>
</footer>

</body>

</html>
