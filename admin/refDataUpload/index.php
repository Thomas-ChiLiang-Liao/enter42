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
    // 資料庫連線，刪除人員介面選單用
  $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
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
  <script src="../scripts.js"></script>
</head>
<body>
  <?php menu('refDataUpload'); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12 col-sm-10 offset-sm-1 col-lg-6 offset-lg-3 col-xxl-4 offset-xxl-4">
        <div class="card mt-5">
          <div class="card-header bg-primary text-center text-white">
            <h3>落點分析資料上傳</h3>
          </div>
          <div class="card-body">
            <form action="writeToMariaDB.php" method="post" enctype="multipart/form-data" onsubmit="return beforeSubmit(this.firstElementChild,'xls_xlsx')">
              <input type="file" class="form-control" id="uploadedFile" name="uploadedFile" onchange="checkFileExtension(this,'xls_xlsx')">
              <button class="btn btn-info mt-2" type="submit">上傳</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>