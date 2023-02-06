<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../../config.ini.php';
    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		$statement = $pdo->prepare('DELETE FROM class WHERE 1;');
		if ( $statement->execute() )	$_SESSION['msg'] = 'success:已清除班級資料表。';
		else 													$_SESSION['msg'] = 'danger:發生錯誤，錯誤代碼：'.$statement->errorInfo()[1].'('.$statement->errorInfo()[0].')<br>訊息：'.$statement->errorInfo()[2];
		header("Location: $_SESSION[projectRoot]/main/");    
  }
}