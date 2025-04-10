<!DOCTYPE html>
<?php
/*
$_SESSION['classTitle'] =>    班級名稱
$_SESSION['studentId'] =>     報名序號(後6碼)
$_SESSION['first'] =>         第一次登入為 true
$_SESSION['studentName'] =>   學生姓名
$_SESSION['examSortID'] =>    報考類別碼
$_SESSION['examSort'] =>      報考類別
$_SESSION['examSols'] =>      可選的類別03,04
$_SESSION['examID'] =>        准考證號
$_SESSION['preChinese'] =>    落點分析國文成績 
$_SESSION['preEnglish'] =>    落點分析英文成績  
$_SESSION['preMath'] =>       落點分析數學成績 
$_SESSION['preProf1'] =>      落點分析專一成績
$_SESSION['preProf2'] =>      落點分析專二成績
$_SESSION['preDeps'] =>       落點分析 
$_SESSION['simInterView'] =>  是否參加模擬面試
$_SESSION['phone1'] =>  
$_SESSION['phone2'] => 
$_SESSION['projectRoot'] => https://yy33.us/enter42/preSelect 
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
    //找出此學生的落點分析資料
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);
    $statement = $pdo->prepare("SELECT *, CONCAT(TVETExamSort.id, TVETExamSort.sort) AS eSort FROM possibileDepartments LEFT JOIN TVETExamSort ON possibileDepartments.examSort = TVETExamSort.id WHERE possibileDepartments.id = :id;");
    $statement->bindParam(':id', $_SESSION['studentId'], PDO::PARAM_STR, 6);
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
  <?php menu('possibileDepartments'); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-md-10 offset-md-1">
        <div class="card">
          <div class="card-header bg-primary text-white text-center">落點分析</div>
          <div class="card-body">
            <table class="table table-hover table-bordered table-sm table-condensed">
              <thead>
                <tr class="bg-secondary text-white">
                  <th class="text-center align-middle">群/類別</th>
                  <th class="text-center align-middle">國文</th>
                  <th class="text-center align-middle">英文</th>
                  <th class="text-center align-middle">數學</th>
                  <th class="text-center align-middle">專一</th>
                  <th class="text-center align-middle">專二</th>
                  <th class="text-center align-middle">落點分析</th>
                </tr>
              </thead>
              <tbody>
                <?php while ( $record = $statement->fetch(PDO::FETCH_ASSOC) ) { ?> 
                <tr>
                  <td class="text-center align-middle"><?php echo $record['eSort']; ?></td>
                  <td class="text-center align-middle"><?php echo $record['chinese'] ?></td>
                  <td class="text-center align-middle"><?php echo $record['english']?></td>
                  <td class="text-center align-middle"><?php echo $record['math']?></td>
                  <td class="text-center align-middle"><?php echo $record['prof1']?></td>
                  <td class="text-center align-middle"><?php echo $record['prof2']?></td>
                  <td><pre><?php echo $record['departments']?></pre></td>
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