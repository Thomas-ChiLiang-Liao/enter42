<!DOCTYPE html>
<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';

    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);
    $sql = "SELECT *, ".
           "CONCAT(possibileDepartments.examSort, TVETExamSort.sort) AS examSortTitle ".
           "FROM possibileDepartments ".
           "LEFT JOIN TVETExamSort ON possibileDepartments.ExamSort = TVETExamSort.id ".
           "WHERE possibileDepartments.id = :id;";
    $statement = $pdo->prepare( $sql );

    $statement->bindParam(':id', $_POST['studentId'], PDO::PARAM_STR, 6);
    $statement->execute();
    $errorInfo = $statement->errorInfo();
    if ($errorInfo[0] != '00000') { $_SESSION['msg'] = "danger: 讀取 possibileDepartment 資料表時發生錯誤，代碼：$errorInfo[0].<br>.訊息代碼：$errorInfo[1]，訊息：$errorInfo[2]。"; header("Location: $_SESSION[projectRoot]/main/"); }
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <title><?php echo $_POST['studentName']; ?>落點分析</title>
  <link rel="icon" href="../../images/<?php echo ( $_SERVER["SERVER_NAME"] == "yy33.us" ? "website-design.png" : "logo.icon.png"); ?>" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
</head>
<body onload="onload()">
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-10 offset-md-1 col-xl-9 offset-xl-2">
        <div class="card">
          <div class="card-header bg-primary text-center text-white">
            <h3><?php echo $_POST['classTitle'].' 【'.$_POST['studentName'].'】' ?>落點分析</h3>
          </div>
          <div class="card-body">
            <table class="table table-bordered">
              <thead>
                <tr class="bg-secondary text-white">
                  <th></th>
                  <th class="text-center large">國文</th>
                  <th class="text-center large">英文</th>
                  <th class="text-center large">數學</th>
                  <th class="text-center large">專一</th>
                  <th class="text-center large">專二</th>
                  <th class="text-center large">落點預測</th>
                </tr>
              </thead>
              <tbody>
                <?php while ( $field = $statement->fetch(PDO::FETCH_ASSOC) ) { ?>
                <tr>
                  <th class="text-center bg-secondary text-white align-middle"><?php echo $field['examSortTitle']; ?><br>成績&落點</th>
                  <td class="text-center align-middle"><?php echo $field['chinese']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['english']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['math']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['prof1']; ?></td>
                  <td class="text-center align-middle"><?php echo $field['prof2']; ?></td>
                  <td class="text-left"><pre><?php echo $field['departments']; ?></pre></td>
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