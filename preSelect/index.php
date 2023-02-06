<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include 'menu.php';
    include '../config.ini.php';
    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$stuId, $stuPw);

    // 要注意 Server 端的時間須減去 Server 端的 Timezone offset
    // 讀取 Server 端的 Timezone offset
    $serverTimezone = date("Z", time());
    $msgPath = "Location: https://$_SERVER[SERVER_NAME]".dirname($_SERVER['SCRIPT_NAME'])."/../error.php?msg";

    // 檢查 enter42.control 中有沒有資料
    // 讀取 enter42.control 中的資料
    // sw1 -> 總開關，sw2 -> 是否超過截止時間，若是則為 OFF。另外還讀取設定之系統截止時間。
    $sql = 'SELECT'
  		. ' IF((switch), "ON", "OFF") AS sw1,'
  		. " IF(((now() - INTERVAL $serverTimezone SECOND) < expire), \"ON\", \"OFF\") AS sw2,"
  		. " expire + INTERVAL $serverTimezone SECOND AS expireDate"
  		. ' FROM control'
  		. ' WHERE 1;';
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $controlTableQueryErrorInfo = $statement->errorInfo();
    if ($controlTableQueryErrorInfo[0] != '00000') { 
      header("$msgPath=danger:讀取 control 資料表發生了錯誤，代碼：$controlTableQueryErrorInfo[0]/$controlTableQueryErrorInfo[1]<br>訊息：$controlTableQueryErrorInfo[2]");
      exit();
    } else {
      if ($statement->rowCount() != 0) {
        $controlTableRow = $statement->fetch(PDO::FETCH_ASSOC);
        $controlTableNULL = false;
      } else $controlTableNULL = true;      
    }

    // 檢查 enter42.student 中有沒有資料
    $statement = $pdo->prepare('SELECT * FROM student WHERE 1;');
    $statement->execute();
    $studentTableQueryErrorInfo = $statement->errorInfo();
    if ($studentTableQueryErrorInfo[0] != '00000') {
      header("$msgPath=danger:讀取 student 資料表發生了錯誤，代碼：$studentTableQueryErrorInfo[0]/$studentTableQueryErrorInfo[1]<br>訊息：$studentTableQueryErrorInfo[2]");
      exit();
    } else $studentTableNULL = ($statement->rowCount() == 0 ? true : false);

    // 檢查 enter42.class 中有沒有資料
    $statement = $pdo->prepare('SELECT * FROM class WHERE 1;');
    $statement->execute();
    $classTableQueryErrorInfo = $statement->errorInfo();
    if ($classTableQueryErrorInfo[0] != '00000') {
      header("$msgPath=danger:讀取 class 資料表發生了錯誤，代碼：$classTableQueryErrorInfo[0]/$classTableQueryErrorInfo[1]<br>訊息：$classTableQueryErrorInfo[2]");
      exit();
    } else $classTableNULL = ($statement->rowCount() == 0 ? true : false);

    if ($controlTableNULL || $studentTableNULL || $classTableNULL) {
      header("$msgPath=warning:網頁尚未建置完成，請依教務處說明辦理。");
      exit();
    }

    if ($controlTableRow['sw1'] == 'OFF') {
      header("$msgPath=warning:網頁因故關閉中，請依教務處說明辦理。");
      exit();
    }

    if ($controlTableRow['sw2'] == 'OFF') {
      header("$msgPath=warning:預選作業須於【$controlTableRow[expireDate]】前完成，現已關閉。");
      exit();
    }
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>預選系統</title>
  <link rel="icon" href="../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../styles.css">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
            <h5 class="text-white p-1"><?php echo (isset($_GET['loginFailed']) ? '帳密錯誤！請重新登入。' : '登入'); ?></h5>
          </div>
          <div class="card-body">
            <form action="login.php" method="post" id="loginForm" onsubmit="beforeSubmit()">
              <input type="hidden" id="browserTimezoneOffset" name="browserTimezoneOffset">
              <input type="text" pattern="[0-9]{8}" class="form-control" placeholder="請輸入准考證號碼" name="examId" id="examId" required title="請輸入八碼數字">
              <input type="password" class="form-control mt-2" placeholder="請輸入密碼" name="pw" id="pw" required>
              <div class="g-recaptcha mt-2" data-sitekey="6LdVj94UAAAAAIVoFG72BRn-xnxIE_bx3uemirm7"></div>
              <button class="btn btn-secondary mt-2" type="submit">登入</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } ?>