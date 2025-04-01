<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../../config.ini.php';
    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
    $pdo->query('DELETE FROM possibileDepartments WHERE 1;');
    if ($pdo->errorInfo()[0] != '00000') $_SESSION['msg'] = 'danger:刪除 Table: enter42.possibileDepartments 發生錯誤，錯誤代碼：'.$pdo->errorInfo()[1].'('.$pdo->errorInfo()[0].')<br>訊息：'.$pdo->errorInfo()[2];
		else {
      $pdo->query('DELETE FROM student WHERE 1;');
      if ( $pdo->errorInfo()[0] == '00000' )	$_SESSION['msg'] = 'success:已清除學生資料表。';
		  else 											              $_SESSION['msg'] = 'danger:刪除 TAble: enter42.student 發生錯誤，錯誤代碼：'.$pdo->errorInfo()[1].'('.$pdo->errorInfo()[0].')<br>訊息A：'.$pdo->errorInfo()[2];
    }
		header("Location: $_SESSION[projectRoot]/main/");  
  }
}