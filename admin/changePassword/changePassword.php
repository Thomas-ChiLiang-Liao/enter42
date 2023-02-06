<?php
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
	session_start();
	if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
	else { 
		include '../../config.ini.php';

    // 消除可能的 SQL Injection
    foreach ($_POST as $i => $data) {
      $data = str_replace('"','',$data);
      $data = str_replace("'","",$data);
      $_POST[$i] = $data;
    }

		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		
		$statement = $pdo->prepare('UPDATE operator SET password = :newPassword WHERE (name = :name AND password = :oldPassword);');
		$statement->bindParam(':newPassword',$_POST['newPw'],PDO::PARAM_STR, 40);
		$statement->bindParam(':name',$_SESSION['name'],PDO::PARAM_STR, 45);
		$statement->bindParam(':oldPassword',$_POST['oldPw'],PDO::PARAM_STR, 40);

		$statement->execute();

		if ($statement->rowCount() == 1) {
			$_SESSION['msg'] = 'success:密碼變更成功！';
			header("Location: $_SESSION[projectRoot]/main");
		} else header("Location:$_SESSION[projectRoot]/changePassword/index.php?error=1");
	}
}
?>