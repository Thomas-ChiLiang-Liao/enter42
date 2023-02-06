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
    foreach ($_POST as $i => $data) {
      $data = str_replace('"','',$data);
      $data = str_replace("'","",$data);
      $_POST[$i] = $data;
    }	
    
    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		$statement = $pdo->prepare('UPDATE student SET password = pw_backup WHERE id = :id;');
		$statement->bindParam(':id', $_POST['studentId'], PDO::PARAM_STR, 6);
    
		if ($statement->execute())	$_SESSION['msg'] = "success:已將【$_POST[classTitle]&nbsp;$_POST[seatNo]&nbsp;$_POST[studentName]】同學的密碼回復成原始設定。";
		else 												$_SESSION['msg'] = "danger:回復【$_POST[classTitle]&nbsp;$_POST[seatNo]&nbsp;$_POST[studentName]】同學密碼時發生錯誤，錯誤代碼：" . $statement->errorCode();
    
		header("Location: $_SESSION[projectRoot]/main/");
  }
}
?>