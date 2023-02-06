<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
	session_start();
	if ( !isset( $_SESSION['studentName'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
	else { 
		include '../../config.ini.php';

    // 消除可能的 SQL Injection
    foreach ($_POST as $i => $data) {
      $data = str_replace('"','',$data);
      $data = str_replace("'","",$data);
      $_POST[$i] = $data;
    }
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);
		
		$statement = $pdo->prepare('UPDATE student SET simInterView=:sw, phone1=:phone1, phone2=:phone2 WHERE student.id=:studentId;');
    if ($_POST['sw'] == 'on') $sw = 1; else $sw = 0;
		$statement->bindParam(':sw', $sw, PDO::PARAM_STR, 1);
		$statement->bindParam(':phone1', $_POST['phone1'], PDO::PARAM_STR, 10);
		$statement->bindParam(':phone2', $_POST['phone2'], PDO::PARAM_STR, 10);
		$statement->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
		if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      $_SESSION['msg'] = "danger:更新資料庫發錯誤！代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]"; 
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    } else {
      $_SESSION['simInterView'] = $_POST['swValue'];
      $_SESSION['phone1'] = $_POST['phone1'];
      $_SESSION['phone2'] = $_POST['phone2'];
      
      if ($_POST['sw']) $_SESSION['msg'] = "success:你登記要參加專業問題模擬面試，且連絡電話分別為$_POST[phone1]及$_POST[phone2]，設定成功。";
      else 							$_SESSION['msg'] = "warning:你不參加專業問題模擬面試！設定成功。";

      header("Location: $_SESSION[projectRoot]/main");
    }
	}
}
?>