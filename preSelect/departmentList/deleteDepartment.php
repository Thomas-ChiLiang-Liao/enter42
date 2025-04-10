<?php
/*
$_SESSION['classTitle'] =>    班級名稱
$_SESSION['studentId'] =>     報名序號(後6碼)
$_SESSION['first'] =>         第一次登入為 true
$_SESSION['studentName'] =>   學生姓名
$_SESSION['examSortID'] =>    報考類別碼
$_SESSION['examSort'] =>      報考類別
$_SESSION['examSols'] =>      可選的類別03,04
$_SESSION['examID'] =>        准考證號
$_SESSION['preChinese'] =>    落點分析國文成績 
$_SESSION['preEnglish'] =>    落點分析英文成績  
$_SESSION['preMath'] =>       落點分析數學成績 
$_SESSION['preProf1'] =>      落點分析專一成績
$_SESSION['preProf2'] =>      落點分析專二成績
$_SESSION['preDeps'] =>       落點分析 
$_SESSION['simInterView'] =>  是否參加模擬面試
$_SESSION['phone1'] =>  
$_SESSION['phone2'] => 
$_SESSION['projectRoot'] => https://yy33.us/enter42/preSelect 
$_SESSION['browserTimezoneOffset'] => -28800 
$_SESSION['serverTimezoneOffset'] => 28800
*/
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['studentName'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';

    foreach ($_POST as $i => $data) {
			$data 			= str_replace('"','',$data);
			$_POST[$i] 	= str_replace("'","",$data);
    }

		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $stuId, $stuPw);	
    
    /*******************************
		* 將傳來的校系資料寫到資料庫中 *
		*******************************/
		// 將此學生及校系資料從 TVERETarget 中刪除
		$sql = "DELETE FROM TVERETarget WHERE TVERETarget.id= :stuDepartmentId";
		$deleteFromTVERETarget = $pdo->prepare($sql);
		$stuDepartmentId = $_SESSION['studentId'] . substr($_POST['deleteDepartmentIdTitle'],0,6);
		$deleteFromTVERETarget->bindParam(':stuDepartmentId', $stuDepartmentId, PDO::PARAM_STR, 12);
    if (!$deleteFromTVERETarget->execute()) {
      $errorInfo = $deleteFromTVERETarget->errorInfo();
      $_SESSION['msg'] = "danger:從 TVERETarget 中刪除資料錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    } 
		
		// 把此異動記錄到 TVEREOperateRecord 中
		$sql = 'INSERT INTO TVEREOperateRecord VALUES (:studentId,"D",now() - INTERVAL :offset SECOND,:departmentId,:remoteIp)';
		$insertIntoTVEREOperateRecord = $pdo->prepare($sql);
		$insertIntoTVEREOperateRecord->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
		$insertIntoTVEREOperateRecord->bindParam(':offset', $_SESSION['serverTimezoneOffset'], PDO::PARAM_STR);
		$departmentId =  substr($_POST['deleteDepartmentIdTitle'], 0, 6);
		$insertIntoTVEREOperateRecord->bindParam(':departmentId', $departmentId, PDO::PARAM_STR, 6);
		$remoteIp = $_SERVER['REMOTE_ADDR'] . (empty($_SERVER['HTTP_CLIENT_IP']) ? "" : "/$_SERVER[HTTP_CLIENT_IP]") . (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "" : "/$_SERVER[HTTP_X_FORWARDED_FOR]"); 
		$insertIntoTVEREOperateRecord->bindParam(':remoteIp', $remoteIp, PDO::PARAM_STR);
    if (!$insertIntoTVEREOperateRecord->execute()) {
      $errorInfo = $insertIntoTVEREOperateRecord->errorInfo();
      $_SESSION['msg'] = "danger:寫入 TVEREOperateRecord 時發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    }
		
    /*******************************************
    * 統計各校系選擇的人數，存入 TVEREStatic 中 *
    *******************************************/
    // 清除舊的統計資料
  	//$query = 'DELETE FROM TVEREStatic WHERE 1; ';
  	$statement = $pdo->query("DELETE FROM TVEREStatic WHERE 1");
  	$statement->execute();
  
  	// 新的統計
  	$sql = 'INSERT INTO TVEREStatic'
  		. ' SELECT RIGHT(TVERETarget.id,6), COUNT(*), NOW() - INTERVAL :offset SECOND FROM TVERETarget'
  		. ' WHERE 1'
  		. ' GROUP BY RIGHT(TVERETarget.id,6);';
  	$statement = $pdo->prepare($sql);
  	$statement->bindParam(':offset', $_SESSION['serverTimezoneOffset'], PDO::PARAM_STR);
  	if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      $_SESSION['msg'] = "danger:寫入 TVEREStatic 時發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      header("Location: $_SESSION[projectRoot]/main");
      exit();
    } 
  	 	
  	$_SESSION['msg'] = "success:<strong>【$_POST[deleteDepartmentIdTitle]】</strong>&nbsp;&nbsp;已加到你的志願中刪除，請由上方功能表繼續操作。";
    header("Location: $_SESSION[projectRoot]/main");
  } 
} 
?>