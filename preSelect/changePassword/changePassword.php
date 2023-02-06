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
		
		$statement = $pdo->prepare('UPDATE student SET password = :newPassword WHERE (examID = :examID AND password = :oldPassword);');
		$statement->bindParam(':newPassword',$_POST['newPw'],PDO::PARAM_STR, 40);
		$statement->bindParam(':examID',$_SESSION['examID'],PDO::PARAM_STR, 8);
		$statement->bindParam(':oldPassword',$_POST['oldPw'],PDO::PARAM_STR, 40);

		$statement->execute();
    
    $errorInfo = $statement->errorInfo();
    
    if ($errorInfo[0] != '00000') {
      $_SESSION['msg'] = "danger:更新資料庫時發生錯誤，代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      header("Location: $_SESSION[projectRoot]/main/");
      exit();
    }
		
    if ($statement->rowCount() == 1) {
      $_SESSION['first'] = 0;
			$_SESSION['msg'] = 'success:密碼變更成功！';
			header("Location: $_SESSION[projectRoot]/main");
		} else header("Location: $_SESSION[projectRoot]/changePassword/index.php?error=1");
	}
}
?>