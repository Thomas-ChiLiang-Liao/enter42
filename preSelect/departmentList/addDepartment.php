<?php
/*
$_SESSION['classTitle']
$_SESSION['studentId']  
$_SESSION['first']
$_SESSION['studentName']
$_SESSION['examSortID'] => 51 
$_SESSION['examSort'] => 電機與電子群 
$_SESSION['examSols'] => 03,04 
$_SESSION['examID']  
$_SESSION['simInterView'] 
$_SESSION['phone1'] 
$_SESSION['phone2']
$_SESSION['projectRoot'] => https://photo.taivs.tp.edu.tw/enter42/preSelect 
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
		/***************************************************
		* 將所選校資料存到資料庫中                         *
		* 同時也會檢查不合邏輯或相關規定，並給使用者訊息。 * 
		***************************************************/		
		
		// 設定已選校系代碼陣列及一校一系代碼陣列
		$sql = 'SELECT'
      	. ' TVEREDepartment.id AS departmentId,'
      	. ' TVERESchool.isRestricted AS isRestricted'
      . ' FROM TVERETarget'
      . ' LEFT JOIN TVEREDepartment ON RIGHT(TVERETarget.id,6) = TVEREDepartment.id'
      . ' LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id'
      . ' WHERE LEFT(TVERETarget.id,6)= :studentId;';
    $selectedTargets = $pdo->prepare($sql);
    $selectedTargets->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
    $selectedTargets->execute();
    
    $selectedTargetArray = array();	
    $selectedSchoolArray = array();
    while ($field = $selectedTargets->fetch(PDO::FETCH_ASSOC)) { 
    	$selectedTargetArray[] = $field['departmentId'];
    	if ($field['isRestricted']) $selectedSchoolArray[] = substr($field['departmentId'],0,3);    	
    }

		// 安全措施：先檢查此生已選校系是否在六個以上！
		if ($selectedTargets->rowCount() < $maxTargets) {
			// 校系是否存在？
			$isDepartmentIdExist = $pdo->prepare("SELECT id FROM TVEREDepartment WHERE id = :departmentId;");
			$isDepartmentIdExist->bindParam(':departmentId', substr($_POST['addDepartmentIdTitle'], 0, 6), PDO::PARAM_STR, 6);
			$isDepartmentIdExist->execute();
			if ($isDepartmentIdExist->rowCount() == 1) {
				// 檢查是否已選
				if (!in_array(substr($_POST['addDepartmentIdTitle'],0,6), $selectedTargetArray)) {
					// 檢查是否屬一校一系的限制
					if (!in_array(substr($_POST['addDepartmentIdTitle'],0,3), $selectedSchoolArray)) {
        		// 將學生及校系資料加到 TVERETarget 中
        		$sql = 'INSERT INTO TVERETarget VALUES (:stuDepartmentId);';
        		$insertIntoTVERETarget = $pdo->prepare($sql);
        		$stuDepartmentId = $_SESSION['studentId'] . substr($_POST['addDepartmentIdTitle'], 0, 6);
        		$insertIntoTVERETarget->bindParam(':stuDepartmentId', $stuDepartmentId, PDO::PARAM_STR, 12);
   					if (!$insertIntoTVERETarget->execute()) {
               $errorInfo = $insertIntoTVERETarget->errorInfo();
               $_SESSION['msg'] = "danger:寫入 TVERETarget 發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
               header("Location: $_SESSION[projectRoot]/main");
               exit();
             }
            
   					
        		// 把此異動記錄到 TVEREOperateRecord 中
        		$sql = 'INSERT INTO TVEREOperateRecord VALUES (:studentId,"A",now() - INTERVAL :offset SECOND,:departmentId,:remoteIp);';
        		$insertIntoTVEREOperateRecord = $pdo->prepare($sql);
        		$insertIntoTVEREOperateRecord->bindParam(':studentId', $_SESSION['studentId'], PDO::PARAM_STR, 6);
        		$insertIntoTVEREOperateRecord->bindParam(':offset', $_SESSION['serverTimezoneOffset'], PDO::PARAM_STR);
        		$departmentId = substr($_POST['addDepartmentIdTitle'], 0, 6);
        		$insertIntoTVEREOperateRecord->bindParam(':departmentId', $departmentId, PDO::PARAM_STR, 6);
        		$remoteIp = $_SERVER['REMOTE_ADDR'] . (empty($_SERVER['HTTP_CLIENT_IP']) ? "" : "/$_SERVER[HTTP_CLIENT_IP]") . (empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? "" : "/$_SERVER[HTTP_X_FORWARDED_FOR]");        		
        		$insertIntoTVEREOperateRecord->bindParam(':remoteIp', $remoteIp, PDO::PARAM_STR);
        		if (!$insertIntoTVEREOperateRecord->execute()) {
              $errorInfo = $insertIntoTVEREOperateRecord->errorInfo();
              $_SESSION['msg'] = "danger:窵入 TVEREOperateRecord 發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
              header("Location: $_SESSION[projectRoot]/main");
              exit();
            }
    
            // 統計各校系選擇的人數，存入 TVEREStatic 中
            // 清除舊的統計資料
          	$statement = $pdo->query("DELETE FROM TVEREStatic WHERE 1;");
          	//$statement->execute();
          	
          	// 新的統計
          	$sql = 'INSERT INTO TVEREStatic'
          		. ' SELECT RIGHT(TVERETarget.id,6), COUNT(*), NOW() - INTERVAL :offset SECOND FROM TVERETarget'
          		. ' WHERE 1'
          		. ' GROUP BY RIGHT(TVERETarget.id,6);';
          	$statement = $pdo->prepare($sql);
          	$statement->bindParam(':offset', $_SESSION['serverTimezoneOffset'], PDO::PARAM_STR);
          	if (!$statement->execute()) {
              $errorInfo = $statement->errorInfo();
              $_SESSION['msg'] = "danger:寫入 TVEREStatic 發生錯誤。代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
              header("Location: $_SESSION[projectRoot]/main");
              exit();
            }
          	
          	$_SESSION['msg'] = "success:<strong>【$_POST[addDepartmentIdTitle]】</strong>&nbsp;&nbsp;已加到你的志願中，請由上方功能表繼續操作。";
          } else $_SESSION['msg'] = "danger:此校只能選一系！請重新操作。";
        } else $_SESSION['msg'] = "danger:此校系已經選過了，請重新操作。";
      } else $_SESSION['msg'] = "danger:校系不存在，請重新操作。";
    } else $_SESSION['msg'] = "danger:預選校系數量不得超過六個，請重新操作。";
    header("Location: $_SESSION[projectRoot]/main");    
  } 
} 
?>