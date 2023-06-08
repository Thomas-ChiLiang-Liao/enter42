<!DOCTYPE html>
<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
    else {
      session_start();
      include '../menu2.php';
      include '../config.ini.php';
      
      foreach ($_GET as $i => $data) {
        $data 			= str_replace('"','',$data);
        $_GET[$i] 	= str_replace("'",'',$data);
      }    
  
      // 資料庫連線
      $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $guestId, $guestPw);

      // 查詢此生的班級座號
      $sql = "SELECT"
          . "   class.title AS classTitle"
          . " FROM class"
          . " WHERE class.id = LEFT(:studentId,3);";
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':studentId', $_GET['stuid'], PDO::PARAM_STR, 6);
      if (!$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        echo "1讀取資料發錯誤！代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
        exit();
      }
      $student = $statement->fetch(PDO::FETCH_ASSOC);

	  	// 查詢此生已選擇的校系
      $sql = 'SELECT'
      	. ' TVEREDepartment.id AS departmentId,'
      	. ' CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,'
      	. ' TVEREDepartment.quotaA AS quotaA,'
    		. ' CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,'
	  		. ' WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,'    	
      	. ' TVEREStatic.num AS students'
      . ' FROM TVERETarget'
      . ' LEFT JOIN TVEREDepartment ON RIGHT(TVERETarget.id,6) = TVEREDepartment.id'
      . ' LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id'
      . ' LEFT JOIN TVEREStatic ON RIGHT(TVERETarget.id,6) = TVEREStatic.DepartmentID'
      . ' WHERE LEFT(TVERETarget.id,6)= :studentId'
      . ' ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;';
      $statement = $pdo->prepare($sql);
      $statement->bindParam(':studentId', $_GET['stuid'], PDO::PARAM_STR, 6);
      if (!$statement->execute()) {
        $errorInfo = $statement->errorInfo();
        echo "2讀取資料發錯誤！代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <title><?php echo "$student[classTitle]【".substr($_GET['stuid'],-2)."】"; ?>預選列表</title>
  <link rel="icon" href="../images/logo.icon.png" type="image/x-icon">
  <link rel="stylesheet" href="../../styles.css">
  <script src="../../autoLogout.js"></script>
</head>
<body>
  <?php menu2(); ?>
  <div class="container-fluid">
    <div class="row mt-5">
      <div class="col-12 col-md-10 offset-md-1">
          <div class="card">
            <div class="card-header text-center text-white bg-primary"><?php echo "$student[classTitle]【座號：".substr($_GET['stuid'],-2)."】預選&nbsp;".$statement->rowCount()."&nbsp;校系"; ?></div>
            <div class="card-body">
              <table class="table table-bordered table-hover table-sm">
                <thead>
                  <tr class="bg-secondary text-white">
                    <th class="text-center">校系名稱</th>
                    <th class="text-center">甄試日期</th>
                    <th class="text-center">招生名額</th>
                    <th class="text-center">預選人數</th>
                  </tr>
                </thead>
                <tbody>
                  <?php while ( $field = $statement->fetch(PDO::FETCH_ASSOC) ) { ?>
                  <tr>
                    <td class="align-middle">
                      <a href="<?php echo $_SESSION['projectRoot'] ?>/departments/departmentDetails.php?depid=<?php echo $field['departmentId']; ?>" title="按此可查詢此校系的資料" target="_blank" class="text-primary fw-bold text-decoration-none">
                        <?php echo $field['departmentId'].$field['title']; ?>  
                      </a>
                    </td>
                    <td class="text-center align-middle"><?php echo ( substr($field['examDate'],0,1) == '-' ? '--' : $field['examDate'].weekDay($field['examDateWeekDay'])); ?></td>      
                    <td class="text-center align-middle"><?php echo $field['quotaA']; ?></td>
                    <td class="text-center align-middle">
                      <?php if (is_null($field['students'])) { ?>
                      無人選
                      <?php } else { ?>
                      <a href="studentsList.php?targetId=<?php echo $field['departmentId']; ?>" class="btn btn-info" title="預選此校系人數，按此可查詢名單" target="_blank"><?php echo $field['students']; ?></a>
                      <?php } ?>
                    </td>            
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
<?php } ?>