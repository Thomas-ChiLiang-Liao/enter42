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

		// 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		// 先讀出系統的設定值
		$statement = $pdo->query("SELECT switch, (expire + INTERVAL $_SESSION[secondsBrowserTimezoneOffset] SECOND) AS expire FROM control WHERE 1;");
		//$statement->execute();
		$field = $statement->fetch(PDO::FETCH_ASSOC);    
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
  <script>
    // 讀取 PHP 變數
    let sw = <?php echo $field['switch']; ?>;
    let expire = "<?php echo $field['expire']; ?>";
  </script>
  <script src="scripts.js"></script>
</head>
<body onload="init()">
  <?php menu('webPageSetting'); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2 col-xl-6 offset-xl-3">
        <div class="card mt-5">
          <div class="card-header bg-primary text-center text-white">
            <h3>預選網頁開關設定</h3>
          </div>
          <div class="card-body">
            <form action="writeToMariaDB.php" method="post">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="sw" name="sw" onclick="checkSwitch(this)">
                <label class="form-check-label" for="sw">開啟網頁</label>
              </div>
              <div class="row" id="timePanel">
                <div class="col-12 col-md-6">
                  <div class="input-group mt-2">
                    <span class="input-group-text">截止日期</span>
                    <input type="date" class="form-control" id="expireDate" name="expireDate">
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="input-group mt-2">
                    <span class="input-group-text">截止時間</span>
                    <input type="time" class="form-control" id="expireTime" name="expireTime">
                  </div>
                </div>
              </div>
              <button class="btn btn-primary mt-2" type="submit">設定</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php
  }
}