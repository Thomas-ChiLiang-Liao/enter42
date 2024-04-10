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
$_SESSION['preChinese']
$_SESSION['preEnglish']
$_SESSION['preMath']
$_SESSION['preProf1']
$_SESSION['preProf2']
$_SESSION['preDeps']
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

    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);

    // 查詢此生的操作紀錄
		$sql = 'SELECT'
    . '   TVEREOperateRecord.action AS action,'
    . '   CONCAT(TVEREDepartment.id, TVERESchool.title, TVEREDepartment.title) AS title,'
    . "   at - INTERVAL $_SESSION[browserTimezoneOffset] SECOND AS actionTime,"
    . '   fromIP AS fromIP'
    . ' FROM TVEREOperateRecord'
    . ' LEFT JOIN TVERESchool ON LEFT(TVEREOperateRecord.departmentId,3) = TVERESchool.id'
    . ' LEFT JOIN TVEREDepartment ON TVEREOperateRecord.departmentId = TVEREDepartment.id'
    . ' WHERE TVEREOperateRecord.studentId = :studentId'
    . ' ORDER BY actionTime ASC;';
  
  $statement = $pdo->prepare($sql);
  $statement->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
  if (!$statement->execute()) {
    $errorInfo = $statement->errorInfo();
    $_SESSION['msg'] = "danger:讀取學生資料時發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
    header("Location: $_SESSION[projectRoot]/main");
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
  <link rel="icon" href="../../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
</head>
<body>
  <?php menu('operateRecords'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-md-10 offset-md-1">
        <div class="card">
          <div class="card-header bg-primary text-center text-white">你的預選紀錄</div>
          <div class="card-body">
            <table class="table table-hover table-bordered table-sm table-condensed">
              <thead class="bg-secondary text-white">
                <tr>
                  <th class="text-center">時間</th>
                  <th class="text-center">動作</th>
                  <th class="text-center">校系代碼及名稱</th>
                  <th class="text-center">來自</th>
                </tr>
              </thead>
              <tbody>
                <?php while($field = $statement->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr class="text-<?php echo ( $field['action'] == 'A' ? 'primary' : 'danger'); ?>">
                  <td class="text-center align-middle"><?php echo $field['actionTime']; ?></td>
                  <td class="text-center align-middle"><?php echo ($field['action'] == 'A' ? '增加' : '刪除'); ?></td>
                  <td class="align-middle<?php echo ($field['action'] == 'A' ? '' : ' text-decoration-line-through'); ?>"><?php echo $field['title']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['fromIP']; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php } } ?>