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

      // 消除可能的 SQL Injection
      foreach ($_POST as $i => $data) {
        $data = str_replace('"','',$data);
        $data = str_replace("'","",$data);
        $_POST[$i] = $data;
      }

		// 操作資料庫
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		$statement = $pdo->query( "DELETE FROM operator WHERE id = $_POST[operatorId];" );
		
		$statement->execute();
		
		if ( $statement->rowCount() == 0 ) 	$_SESSION['msg'] = "success:刪除人員成功。";
		else 																$_SESSION['msg'] = "danger:刪除人員失敗！";
	
		header("Location: $_SESSION[projectRoot]/main");
    } 
  } 
?>