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
    
    // 檢查網頁是否已經關閉
    $sql = 'SELECT'
  		. ' IF((switch), "ON", "OFF") AS sw1,'
  		. " IF(((now() - INTERVAL $_SESSION[serverTimezoneOffset] SECOND) < expire), \"ON\", \"OFF\") AS sw2"
  		. ' FROM control'
  		. ' WHERE 1;';
    $statement = $pdo->query($sql);
    $errMessage = $pdo->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $_SESSION['msg'] = "danger:讀取 control 資料表發生錯誤！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    }
    $field = $statement->fetch(PDO::FETCH_ASSOC);
    if ($field['sw1'] == 'OFF' || $field['sw2'] == 'OFF') { $_SESSION['msg'] = "warning:網站已關閉！"; header("Location: $_SESSION[projectRoot]/main"); exit(); }   


    /*************************************************************************
    ** 先判斷要不要統計各校系選填人數                                       **
    ** 取出統計表(TVEREStatic)及操作紀錄表(TVEREOperateRecord )中的最後時間 **
    *************************************************************************/
    /*
    $statement = $pdo->query("SELECT MAX(at) AS time FROM TVEREOperateRecord;");
    $errMessage = $pdo->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $_SESSION['msg'] = "danger:讀取 TVEREOperateRecord 資料表發生錯誤(行號：53)！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    }
 		$field = $statement->fetch(PDO::FETCH_ASSOC);
    $lastOperateRecordAt = mysqlDateTime2PHPTimeInteger($field['time']);

    $statement = $pdo->query("SELECT MAX(at) AS time FROM TVEREStatic;");
    $errMessage = $pdo->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $_SESSION['msg'] = "danger:讀取 TVEREStatic 資料表發生錯誤(行號：63)！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    }
		$field = $statement->fetch(PDO::FETCH_ASSOC);
    $staticAt = mysqlDateTime2PHPTimeInteger($field['time']);		
    */
    
		// 查詢此生已選擇的校系
    $sql = 'SELECT'
    	. ' TVEREDepartment.id AS departmentId,'
    	. ' CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,'
    	. ' TVEREDepartment.quotaA AS quotaA,'
  		. ' CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,'
			. ' WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,'    	
    	. ' TVEREStatic.num AS students,'
    	. ' TVERESchool.maxTargets AS maxTargets'
    . ' FROM TVERETarget'
    . ' LEFT JOIN TVEREDepartment ON RIGHT(TVERETarget.id,6) = TVEREDepartment.id'
    . ' LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id'
    . ' LEFT JOIN TVEREStatic ON RIGHT(TVERETarget.id,6) = TVEREStatic.DepartmentID'
    . ' WHERE LEFT(TVERETarget.id,6)= :studentId'
    . ' ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;';
    $selectedTargets = $pdo->prepare($sql);
    $selectedTargets->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
    $selectedTargets->execute();  
    $errMessage = $selectedTargets->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $_SESSION['msg'] = "danger:讀取 TVERETarget 發生錯誤！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();      
    }

    // 統計此生各校已選系科組之數量
    $sql = 'SELECT'
      . '   TVERESchool.id AS schId,'
      . '   TVERESchool.title AS schName,'
      . '   COUNT(*) AS targets'
      . ' FROM TVERETarget'
      . ' LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id'
      . " WHERE LEFT(TVERETarget.id,6) = '$_SESSION[studentId]'"
      . ' GROUP BY MID(TVERETarget.id,7,3);';
    $statement = $pdo->query($sql);
    $errMessage = $pdo->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $msg = "danger:讀取 TVERETargets 資料表發生錯誤！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
      header("Location: ../error.php?msg=".$msg);
      exit();
    }
    
    // 讀出指定學生選擇各校的系科組數，存入 $targetsOfSchoolArray 中。
    $targetsOfSchoolArray = array();
    while ($field = $statement->fetch(PDO::FETCH_ASSOC)) $targetsOfSchoolArray[$field['schId']] = $field['targets'];
    
    // 將已選擇的校系及一校一系且有被選擇的學校分別存入 $selectedTargetArray 及 $selectedSchoolArray 中。
    $selectedTargetArray = array();
    //$selectedSchoolArray = array();
    // 設定上兩個陣列，在已選擇校系列表中一起設定

  	// 查詢此學生可選擇的校系
    // 先設定 where 字句
    $whereClause = '';
    $examSortArray = explode(',', $_SESSION['examSols']);
    foreach ($examSortArray AS $i => $data) {
      if ($i != 0) $whereClause .= ' OR ';
      $whereClause .= "TVEREDepartment.examSort = '$data'";
    } 		
  	$sql = 'SELECT'
  		. ' TVEREDepartment.id AS departmentId,'
  		. ' CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,'
  		. ' TVEREDepartment.quotaA AS quotaA,'
  		. ' CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,'
			. ' WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,'
  		. ' TVEREStatic.num as students,'
  		. ' TVERESchool.maxTargets AS maxTargets'
  	. ' FROM TVEREDepartment'
  	. ' LEFT JOIN TVERESchool ON TVERESchool.id = LEFT(TVEREDepartment.id, 3)'
  	. ' LEFT JOIN TVEREStatic ON TVEREDepartment.id = TVEREStatic.DepartmentID'
  	. " WHERE ($whereClause) AND TVEREDepartment.quotaA != 0"
    . ' ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;';
  	$selectableTargets = $pdo->query($sql);  
    //$selectableTargets->execute();
    $errMessage = $pdo->errorInfo();
    if ( $errMessage[0] != '00000' ) {
      $_SESSION['msg'] = "danger:讀取 TVERETarget 發生錯誤！代碼：$errMessage[0]/$errMessage[1]<br>訊息：$errMessage[2]";
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
  <script src="scripts.js"></script>
</head>
<body>
  <?php menu('departmentList'); ?>
  <div class="container-fluid">
    <?php if ($selectedTargets->rowCount() != 0) { ?>
    <form action="studentsList.php" id="studentsListForm" method="post">
      <input type="hidden" id="targetId" name="targetId">
    </form>
    <!-- 已選擇校系列表 -->
    <div class="row mt-5">
      <div class="col-12 col-md-10 offset-md-1">
        <div class="card">
          <div class="card-header text-center text-white bg-warning">你已經選擇的校系【<?php echo $selectedTargets->rowCount(); ?>】</div>
          <div class="card-body">
            <form action="deleteDepartment.php" id="deleteTargetForm" method="post">
              <input type="hidden" id="deleteDepartmentIdTitle" name="deleteDepartmentIdTitle">
            </form>
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="bg-secondary text-white">
                  <th class="text-center align-middle">刪除按扭</th>
                  <th class="text-center align-middle">校系名稱</th>
                  <th class="text-center align-middle">甄試日期</th>
                  <th class="text-center align-middle">招生名額</th>
                  <th class="text-center align-middle">預選人數</th>
                  <th class="text-center align-middle">可選填報名之<br>校系科(組)、學程數</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                // 將此生已選擇的校系存到 selectedTargetArray 中，在可選擇校系列表中不再次出現。
                while ( $field = $selectedTargets->fetch(PDO::FETCH_ASSOC) ) {
                  $selectedTargetArray[] = $field['departmentId'];
                  //if ( $field['isRestricted'] ) $selectedSchoolArray[] = substr( $field['departmentId'], 0, 3 );
                ?>
                <tr>
                  <td class="text-center align-middle">
                    <button class="btn btn-warning" type="button" onclick="deleteTarget('<?php echo $field['departmentId']; ?>','<?php echo $field['title'] ?>')">刪除</button>
                  </td>
                  <td class="align-middle">
                    <a href="../../departments/departmentDetails.php?depid=<?php echo $field['departmentId']; ?>" title="按此可查詢此校系的資料" target="_blank" class="text-primary fw-bold text-decoration-none">
                      <?php echo $field['departmentId'].$field['title']; ?>  
                    </a>
                  </td>
                  <td class="text-center align-middle"><?php echo ( substr($field['examDate'],0,1) == '-' ? '--' : $field['examDate'].weekDay($field['examDateWeekDay'])); ?></td>      
                  <td class="text-center align-middle"><?php echo $field['quotaA']; ?></td>
                  <td class="text-center align-middle">
                    <?php if (is_null($field['students'])) { ?>
                    無人選
                    <?php } else { ?>
                    <button class="btn btn-info" title="預選此校系人數，按此可查詢名單" onclick="studentsList('<?php echo $field['departmentId']; ?>')"><?php echo $field['students']; ?></button>
                    <?php } ?>
                  </td>            
                  <td class="text-center align-middle"><?php echo $field['maxTargets']; ?></td>
                </tr>
                <?php } ?>  
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <? } ?>
    <!-- 可選擇校系列表 -->
    <div class="row mt-5">
      <div class="col-12 col-md-10 offset-md-1">
        <div class="card">
          <div class="card-header text-center text-white bg-primary">
            你可以選擇的校系【<?php echo $selectableTargets->rowCount() - $selectedTargets->rowCount(); ?>】
          </div>
          <div class="card-body">
            <form action="addDepartment.php" id="addTargetForm" method="post">
              <input type="hidden" id="addDepartmentIdTitle" name="addDepartmentIdTitle">
            </form>
            <table class="table table-bordered table-hover table-sm">
              <thead>
                <tr class="bg-secondary text-white">
                  <th class="text-center align-middle">新增按鈕</th>
                  <th class="text-center align-middle">校系名稱</th>
                  <th class="text-center align-middle">招生名額</th>
                  <th class="text-center align-middle">預選人數</th>
                  <th class="text-center align-middle">可選填報名之<br>校系科(組)、學程數</th>
                </tr>
              </thead>             
              <tbody>
                <?php
                // 此生已選擇的校系存在 selectedTargetArray 中，在可選擇校系列表中不要再次出現
                $rowCounter = 0;
                while ($field = $selectableTargets->fetch(PDO::FETCH_ASSOC)) {
                  if (in_array($field['departmentId'], $selectedTargetArray)) continue;
                ?>
                <tr>
                  <td class="text-center align-middle">
                    <?php
                    // 預選總數 < $maxTargets 且 選擇此志願學校的數目小於限制
                    // if ($selectedTargets->rowCount() < $maxTargets && !in_array(substr($field['departmentId'],0,3), $selectedSchoolArray)) {
                    $schId = substr($field['departmentId'], 0, 3);
                    if ( $selectedTargets->rowCount() < $maxTargets && ( array_key_exists( $schId, $targetsOfSchoolArray ) ? ( $targetsOfSchoolArray[$schId] < $field['maxTargets'] ? true  : false ) : true ) ) {  
                    ?>  
                    <button class="btn btn-primary" type="button" onclick="addTarget('<?php echo $field['departmentId']; ?>', '<?php echo $field['title']; ?>')">加入</button>
                    <?php } ?>
                  </td>
                  <td class="align-middle">
                    <a href="../../departments/departmentDetails.php?depid=<?php echo $field['departmentId']; ?>" title="按此可查詢此校系資料" target="_blank"  class="text-primary fw-bold text-decoration-none">
                      <?php echo $field['departmentId'].$field['title']; ?>
                    </a>
                  </td>
                  <td class="text-center align-middle"><?php echo $field['quotaA']; ?></td>
                  <td class="text-center align-middle">
                  <?php if (is_null($field['students'])) { ?>
                    無人選
                  <?php } else { ?>
                    <button class="btn btn-info" title="預選此校系人數，按此可查詢名單" onclick="studentsList('<?php echo $field['departmentId']; ?>')"><?php echo $field['students']; ?></button>
                  <?php } ?>
                  </td>
                  <td class="text-center align-middle"><?php echo $field['maxTargets']; ?></td>
                </tr>
                <?php if (++$rowCounter % 15 == 0) { ?>
                <tr  class="bg-secondary text-white">
                  <th class="text-center align-middle">新增按鈕</th>
                  <th class="text-center align-middle">校系名稱</th>
                  <th class="text-center align-middle">招生名額</th>
                  <th class="text-center align-middle">預選人數</th>
                  <th class="text-center align-middle">可選填報名之<br>校系科(組)、學程數</th>
                </tr>
                <?php } } ?>    
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