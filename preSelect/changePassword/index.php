<!DOCTYPE html>
<?php
/*
$_SESSION['classTitle']
$_SESSION['studentId']  
$_SESSION['first']
$_SESSION['studentName']
$_SESSION['examSortID'] => 51 
$_SESSION['examSort'] => 電機與電子群 
$_SESSION['examSols'] => 03,04 
$_SESSION['examID']  
$_SESSION['simInterView'] 
$_SESSION['phone1'] 
$_SESSION['phone2']
$_SESSION['projectRoot'] => https://photo.taivs.tp.edu.tw/enter42/preSelect 
$_SESSION['browserTimezoneOffset'] => -28800 
$_SESSION['serverTimezoneOffset'] => 28800
*/
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['studentName'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
  <script src="../../sha1.js"></script>
  <script src="../../pw.js"></script>
</head>
<body>
  <?php menu('changePassword'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="card">
          <div class="card-header <?php echo ( isset($_GET['error']) ? 'bg-danger' : 'bg-warning' ); ?>">
            <h5 class="text-white"><?php echo ( isset($_GET['error']) ? '變更密碼失敗！請重新操作。' : '設定新密碼' ); ?></h5>
          </div>
          <div class="card-body">
            <form action="changePassword.php" method="post" onsubmit="encryptPw()">
              <input type="password" class="form-control" placeholder="請輸入舊密碼" id="oldPw" name="oldPw" required>
              <input type="password" class="form-control mt-2" placeholder="請輸入新密碼" id="newPw" name="newPw" onkeyup="checkNewPassword('newPw','confirmPw')" required>
              <input type="password" class="form-control mt-2" placeholder="請再次輸入新密碼" id="confirmPw" name="confirmPw" onkeyup="checkNewPassword('newPw','confirmPw')" required>
              <button class="btn btn-primary mt-2" type="submit" id="setNewPasswordButton" disabled>設定新密碼</button>
            </form>
          </div>
          <?php if ($_SESSION['first'] == 1) { ?>
          <div class="card-foot">
            <h5 class="alert alert-info text-center"><strong>首次登入！</strong>請立刻修改密碼。</h5>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>