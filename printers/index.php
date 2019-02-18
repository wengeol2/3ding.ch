<!doctype html>
<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');
?>
<html lang="en" class="h-100">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Olivier Wenger">
    <title>3Ding.ch - 3D Druck und Beratung</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/regular.css" integrity="sha384-4Cp0kYV2i1JFDfp6MQAdlrauJM+WTabydjMk5iJ7A9D+TXIh5zQMd5KXydBCAUN4" crossorigin="anonymous">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.7.1/css/fontawesome.css" integrity="sha384-iD1qS/uJjE9q9kecNUe9R4FRvcinAvTcPClTz7NI8RI5gUsJ+eaeJeblG1Ex0ieh" crossorigin="anonymous">
    <script src="stl_viewer.min.js"></script>
    <link rel="stylesheet" href="/css/3ding.css">
    <!-- Custom styles for this template -->
    <link href="/css/sticky-footer-navbar.css" rel="stylesheet">

  </head>
  <body class="d-flex flex-column h-100">
    <?php
        // === TOP NAVIGATION ===
        $activePage = "printers";
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/navigation.php');
    ?>
    <script>
        function _(el) {
            return document.getElementById(el);
        }
    </script>
    <!-- Begin page content -->
    <main role="main" class="flex-shrink-0">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mt-5">Printers</h1>
                    <p class="lead">...</p>
                </div>
            </div>
        </div>
    </main>
    <?php
        // === FOOTER ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/footer.php');
    ?>
    <?php
        // === LOGIN MODAL ===
        require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/loginModal.php');
    ?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<!--script>window.jQuery || document.write('<script src="/js/vendor/jquery-slim.min.js"><\/script>')</script-->
<script src="/js/bootstrap.bundle.min.js" integrity="sha384-zDnhMsjVZfS3hiP7oCBRmfjkQC4fzxVxFhBx8Hkz2aZX8gEvA/jsP3eXRCvzTofP" crossorigin="anonymous"></script></body>
</html>
