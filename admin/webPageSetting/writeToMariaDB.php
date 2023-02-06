<?php
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
		
		// 如果有資料，要刪除舊資料
		$statement = $pdo->query('SELECT * FROM control WHERE 1;');
		if ( $statement->rowCount() != 0 ) $statement = $pdo->query('DELETE FROM control WHERE 1;');
    
    // 寫入新的資料
    $statement = $pdo->prepare('INSERT INTO control (switch, expire) VALUES (:switch, TIMESTAMPADD(SECOND,:offset,:expireDateTime));');
		$offset = -$_SESSION['secondsBrowserTimezoneOffset'];
    $expireDateTime = $_POST['expireDate'] . ' ' . $_POST['expireTime'];
    $sw = ( $_POST['sw'] == 'on' ? 1 : 0 );
		
		$statement->bindParam(':switch', $sw, PDO::PARAM_STR, 1);
		$statement->bindParam(':offset', $offset, PDO::PARAM_INT, 5);
		$statement->bindParam(':expireDateTime', $expireDateTime, PDO::PARAM_STR, 19);
    $statement->execute();
    $errorInfo = $statement->errorInfo();
    if ( $errorInfo[0] == '00000' ) $_SESSION['msg'] = ( $sw == 1 ? "success:開啟網頁，並設定操作截止時間為：$expireDateTime" : "success:網頁已關閉" );
    else                            $_SESSION['msg'] = "danger:寫入 control 資料表發生錯誤！代碼：$errorInfo[0]<br>錯誤碼：$errorInfo[1]，訊息：$errorInfo[2]";                 

    header("Location: $_SESSION[projectRoot]/main/");
  }
}
?>