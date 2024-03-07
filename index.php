<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) 
    header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    session_start();
    $_SESSION['projectRoot'] = "https://$_SERVER[SERVER_NAME]".dirname($_SERVER['SCRIPT_NAME']);  
    include 'menu.php';
    include 'config.ini.php';
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php menu(''); ?>
</body>
</html>
<?php } ?>