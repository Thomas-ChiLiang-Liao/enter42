<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    session_start();
    $_SESSION['projectRoot'] = "https://$_SERVER[SERVER_NAME]".dirname($_SERVER['SCRIPT_NAME']);  
    include 'menu.php';
    include '../config.ini.php';
    // 讀出 operator 中的所有資料
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$opId,$opPw);
    $statement = $pdo->query('SELECT id,name FROM operator WHERE 1;');
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統-管理</title>
  <link rel="icon" href="../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
  <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
  <script src="../sha1.js"></script>
  <script src="scripts.js"></script>
</head>
<body>
	<?php menu(''); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
        <div class="card mx-xl-5">
          <div class="card-header <?php echo (isset($_GET['loginFailed']) ? 'bg-danger' : 'bg-primary'); ?>">
            <h5 class="text-white p-1"><?php echo ( isset($_GET['loginFailed']) ? '帳密錯誤！請重新登入。' :  '登入'); ?></h5>
          </div>
            <div class="card-body">
              <form action="login.php" method="post" id="loginForm">
                <input type="hidden" id="secondsBrowserTimezoneOffset" name="secondsBrowserTimezoneOffset">
                <input type="hidden" name="userId" id="userId">
                <input type="text" class="form-control" placeholder="請輸入姓名" list="operators" name="userName" id="userName">
                <datalist id="operators">
                  <?php while ($operator = $statement->fetch(PDO::FETCH_ASSOC)) echo "<option data-value='$operator[id]' value='$operator[name]'>"; ?>
                </datalist>
                <input type="password" class="form-control mt-2" placeholder="請輸入密碼" name="userPw" id="userPw">
                <!--<div class="g-recaptcha mt-2" data-sitekey="6LdVj94UAAAAAIVoFG72BRn-xnxIE_bx3uemirm7"></div>-->
                <button class="btn btn-secondary mt-2" type="button" onclick="getUserId()">登入</button>
              </form>
          </div>        
        </div>
      </div>
    </div>
  </div>

</body>
</html>
<?php } ?>