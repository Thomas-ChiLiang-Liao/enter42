<?php
  if ( !isset( $_SERVER['HTTP_X_HTTPS'] ) OR ( $_SERVER['HTTP_X_HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]$_SERVER[REQUEST_URI]" );
  else {
    include 'menu.php';
    include '../config.ini.php';
  
    // 消除可能的 SQL Injection
    foreach ($_POST as $i => $data) {
      $data = str_replace('"','',$data);
      $data = str_replace("'","",$data);
      $_POST[$i] = $data;
    }
  
    // 資料庫連線
    $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8",$stuId, $stuPw);
    $sql = 'SELECT'
		. ' class.title AS classTitle,'
		. ' student.id AS studentId,'
    . ' IF(student.password = student.pw_backup, 1, 0) AS first,'
		. ' student.name AS studentName,'
		. ' student.examSort AS examSortID,'
		. ' TVETExamSort.Sort AS examSort,'
		. ' TVETExamSort.admissionIds AS examSols,'
		. ' student.examID AS examID,'
		. ' student.simInterView AS simInterView,'
		. ' student.phone1 AS phone1,'
		. ' student.phone2 AS phone2'
		. ' FROM student'
		. ' LEFT JOIN class ON LEFT(student.id,3) = class.id'
		. ' LEFT JOIN TVETExamSort ON student.examSort = TVETExamSort.id'
		. ' WHERE student.examID = :examID AND student.password = :password;';
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':examID', $_POST['examId'], PDO::PARAM_STR, 8);
    $statement->bindParam(':password', $_POST['pw'], PDO::PARAM_STR, 40);
    $statement->execute();
    $errorInfo = $statement->errorInfo();
    if ($errorInfo[0] != '00000') {
      header("$msgPath=danger:讀取資料庫時發生了錯誤，代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]");
      exit();
    } else if ($statement->rowCount() != 1) header("Location: https://$_SERVER[SERVER_NAME]" . dirname($_SERVER['SCRIPT_NAME']) . '/index.php?loginFailed');
           else {
             $student = $statement->fetch(PDO::FETCH_ASSOC);
             // 寫入 $_SESSION 中。
             session_start();
             $_SESSION = $student;
             $_SESSION['projectRoot'] = "https://$_SERVER[SERVER_NAME]" . dirname($_SERVER['SCRIPT_NAME']);
             $_SESSION['browserTimezoneOffset'] = $_POST['browserTimezoneOffset'] * 60;
             $_SESSION['serverTimezoneOffset']  = date("Z", time());

             if ($student['first'] == 1) header("Location: $_SESSION[projectRoot]/changePassword/");
             else header ("Location: $_SESSION[projectRoot]/main/");
           }     
  }
?>