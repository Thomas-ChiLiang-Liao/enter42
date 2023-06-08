<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    session_start();
    include '../menu.php';
    include '../config.ini.php';
    
    foreach ($_POST as $i => $data) {
      $data 			= str_replace('"','',$data);
      $_POST[$i] 	= str_replace("'",'',$data);
    }    

    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);

    // 取出所有招生類別
    $sql = "SELECT * FROM TVETExamSort WHERE id <= 21;";
    $statement = $pdo->query($sql);

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
</head>
<body>
	<?php menu('listByExamSort'); ?>
  <div class="container-fluid">
    <div class="row mt-1">
      <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xxl-4 offset-xxl-4">
        <form action="index.php" method="post">
            <select class="form-select form-select-sm mt-1" name="selector" onchange="this.parentNode.submit()">
              <option value='00'>請選擇招生群(類)別</option>
              <?php while ( $row = $statement->fetch(PDO::FETCH_ASSOC) ) 
              echo "<option".( substr($_POST['selector'],0,2) == $row['id'] ? ' selected' : '')." value='$row[id]$row[sort]'>$row[id]$row[sort]</option>"; ?>
            </select>
        </form>
      </div>
    </div>
    <?php
    if (isset($_POST['selector']) && $_POST['selector'] != '00') {
      $sql = 'SELECT'
      . ' TVEREDepartment.id AS depid,'
      . ' CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,'
      . ' TVEREDepartment.quotaA AS quotaA,'
      . ' CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,'
      . ' WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,'    		
      . ' TVEREStatic.num as students'
    . ' FROM TVEREDepartment'
    . ' LEFT JOIN TVERESchool ON TVERESchool.id = LEFT(TVEREDepartment.id, 3)'
    . ' LEFT JOIN TVEREStatic ON TVEREDepartment.id = TVEREStatic.DepartmentID'
    . ' WHERE TVEREDepartment.examSort = :examSort AND TVEREDepartment.quotaA <> 0'
    . ' ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;';
    $statement = $pdo->prepare($sql);
    $examSort = substr($_POST['selector'],0,2);
    $statement->bindParam(':examSort', $examSort, PDO::PARAM_STR, 2);
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      echo "讀取資料發錯錯誤，代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      exit();
    }
    ?>
    <div class="row mt-2">
      <div class="col-12 col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header bg-primary text-white text-center"><?php echo substr($_POST['selector'],2).'&nbsp;預選統計(計有'.$statement->rowCount().'校系)'; ?></div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-hover">
              <thead>
                <tr class="bg-secondary text-white">
                  <th class="text-center">校系代碼及名稱</th>
                  <th class="text-center">甄試日期</th>
                  <th class="text-center">招生人數</th>
                  <th class="text-center">預選人數</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($field = $statement->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr>
                  <td class="align-middle">
                    <a href="https:../departments/departmentDetails.php?depid=<?php echo $field['depid']; ?>" class="text-primary fw-bold text-decoration-none" title="按下可查詢此系條件" target="_blank">
                      <?php echo "$field[depid]$field[title]"; ?>
                    </a>
                  </td>
                  <td class="text-center align-middle"><?php echo $field['examDate'].weekDay($field['examDateWeekDay']); ?></td>
                  <td class="text-center align-middle"><?php echo $field['quotaA']; ?></td>
                  <td class="text-center">
                    <?php if ($field['students'] != 0) { ?>
                      <a href="../listByClass/studentsList.php?targetId=<?php echo $field['depid']; ?>" class="btn btn-info" title="預選此校系人數，按此可查詢名單" target="_blank"><?php echo $field['students']; ?></a>
                    <?php } else echo '無人選'; ?>
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</body>
</html>
<?php } ?>