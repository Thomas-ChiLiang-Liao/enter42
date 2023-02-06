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
		$statement = $pdo->prepare( 'INSERT INTO operator (name, password, sex, optype) VALUES (:name, :password, :sex, :optype);' );
		
		$statement->bindParam(':name', 			$_POST['operatorName'], 		PDO::PARAM_STR, 10);
		$statement->bindParam(':password', 	$_POST['operatorPassword'], PDO::PARAM_STR, 40);
		$statement->bindParam(':sex', 			$_POST['operatorSex'], 			PDO::PARAM_STR, 1);
		$statement->bindParam(':optype', 		$_POST['operatorType'], 		PDO::PARAM_STR, 1);
		
		$statement->execute();
		
		if ( $statement->rowCount() == 1 ) 	$_SESSION['msg'] = "success:新增人員【$_POST[operatorName]】成功！";
		else 																$_SESSION['msg'] = "danger:發生錯誤，代碼：$statement->errorCode()，請與電腦中心聯絡。";
	
		header("Location: $_SESSION[projectRoot]/main");
    } 
  } 
?>