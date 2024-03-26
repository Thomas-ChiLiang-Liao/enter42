<?php
/*
$_SESSION['projectRoot'] => https://photo.taivs.tp.edu.tw/enter42/admin
$_SESSION['name']
$_SESSION['optype']
$_SESSION['secondsBrowserTimezoneOffset']
$_SESSION['secondsServerTimezoneOffset']
*/
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-管理</title>
  <link rel="icon" href="../../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
  <style>
    a.nav-link.d-inline:link, a.nav-link.d-inline:visited { color: white }
    td a:link, a.visited { color: blue }
  </style>
</head>
<body>
  <?php menu(''); ?>
  <div class="container-fluid">
    <?php
			if (isset($_SESSION['msg'])) { 
				$message = explode(':',$_SESSION['msg']);
				if ( count($message) > 2) {
					foreach ($message as $i => $data) {
						if ($i <= 1) continue;
						else $message[1] .= ':' . $data;
					}
				}    
    ?>
    <div class="row mt-5">
      <div class="col-12">
        <div class="alert alert-<?php echo $message[0]; ?> text-center"><?php echo $message[1]; ?></div>
      </div>
    </div>
    <?php unset($_SESSION['msg']); } ?>
  </div>
</body>
</html>
<?php } } ?>