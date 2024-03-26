<!DOCTYPE html>
<?php
/*
 $_SESSION[projectRoot] => https://photo.taivs.tp.edu.tw/enter42/admin 
 $_SESSION[name] 
 $_SESSION[optype] 
 $_SESSION[secondsBrowserTimezoneOffset]
 $_SESSION[secondsServerTimezoneOffset]
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
  <script src="../../sha1.js"></script>
  <script src="../../autoLogout.js"></script>
  <script src="../../pw.js"></script>
</head>
<body>
  <?php menu('changePassword'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="card">
          <div class="card-header <?php echo ( isset($_GET['error'] ) ? 'bg-danger' : 'bg-warning' ); ?>">
            <h5 class="text-white"><?php echo ( isset($_GET['error'] ) ? '變更密碼失敗！請重新操作'  : '變更密碼作業' ); ?></h5>
          </div>
          <div class="card-body">
            <form action="changePassword.php" method="post" onsubmit="encryptPw()">
              <input type="password" class="form-control" placeholder="請輸入舊密碼" id="oldPw" name="oldPw">
              <input type="password" class="form-control mt-2" placeholder="請輸入新密碼" id="newPw" name="newPw" onkeyup="checkNewPassword('newPw','confirmPw')">
              <input type="password" class="form-control mt-2" placeholder="請再次輸入新密碼" id="confirmPw" name="confirmPw" onkeyup="checkNewPassword('newPw','confirmPw')">
              <button class="btn btn-primary mt-2" type="submit" id="setNewPasswordButton" disabled>送出</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>