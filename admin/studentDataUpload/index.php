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
    // 讀取 enter42.student 中的資料筆數
    $statement = $pdo->query('SELECT * FROM student WHERE 1;');
    $numOfStudents = $statement->rowCount();
    // 讀取 enter42.class 中的資料筆數
    $statement = $pdo->query('SELECT * FROM class WHERE 1;');
    $numOfClasses = $statement->rowCount();
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
  <script src="scripts.js"></script>
</head>
<body>
  <?php menu('studentDataUpload'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-6">
        <!-- 學生資料上傳介面 -->
        <div class="col-lg-8 offset-lg-2">
          <div class="alert alert-info text-center">學生資料表中有【<?php echo $numOfStudents; ?>】筆資料</div>
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h4><?php echo ( $numOfStudents == 0 ? '學生資料上傳' : '清除學生資料' ); ?></h4>
            </div>
            <div class="card-body">
              <?php if ($numOfStudents == 0) { ?> 
                <form action="studentFileUpload.php" method="post" enctype="multipart/form-data">
                  <input type="file" class="form-control" id="uploadedFile" name="uploadedFile">
                  <button class="btn btn-info mt-2" type="submit">上傳</button>
                </form>
              <?php } else { ?>
                <form action="clearStudentTable.php" method="post">
                  <button class="btn btn-info" type="submit">清除學生資料</button>
                </form>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-6">
        <!-- 班級資料上傳介面 -->
        <div class="col-lg-8 offset-lg-2">
          <div class="alert alert-info text-center">班級資料表中有【<?php echo $numOfClasses; ?>】筆資料</div>
          <div class="card">
            <div class="card-header bg-primary text-white">
              <h4><?php echo ( $numOfClasses == 0 ? '班級資料上傳' : '清除班級資料' ); ?></h4>
            </div>
            <div class="card-body">
              <?php if ($numOfClasses == 0) { ?>
                <form action="classFileUpload.php" method="post" enctype="multipart/form-data">
                  <input type="file" class="form-control" id="uploadedFile" name="uploadedFile">
                  <button class="btn btn-info mt-2" type="submit">上傳</button>
                </form>
              <?php } else { ?>
                <form action="clearClassTable.php" method="post">
                  <button class="btn btn-info" type="submit">清除班級資料</button>
                </form>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>