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
      include '../../config.ini.php';
      // 資料庫連線，刪除人員介面選單用
		  $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);

      // 所有學生
      $studentsListQuery = $pdo->query("SELECT student.id AS stuid, student.name AS stuName, student.examSort AS examSort FROM student WHERE 1 ORDER BY student.id;");
      $csv = "甄選報名序號,姓名,統測報名類別,校系科組學程代碼1,校系科組學程代碼2,校系科組學程代碼3,校系科組學程代碼4,校系科組學程代碼5,校系科組學程代碼6\n";
      $studentCounter = 0;

      while ($student = $studentsListQuery->fetch(PDO::FETCH_ASSOC)) {
        $statement = $pdo->prepare("SELECT RIGHT(TVERETarget.id,6) AS depid FROM TVERETarget WHERE LEFT(TVERETarget.id,6) = :stuid ORDER BY depid;");
        $statement->bindParam(':stuid', $student['stuid'], PDO::PARAM_STR, 6);
        if (!$statement->execute()) {
          $errorInfo = $statement->errorInfo();
          echo "讀取資料發生錯誤！代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
          exit();
        } else {
          // 如果此生沒有預選就跳過不處理
          if ($statement->rowCount() == 0) continue;
          $studentCounter++;
          $csv .= "=\"$vhSchoolId-$student[stuid]\"";
          $csv .= ",$student[stuName]";
          $csv .= ",=\"$student[examSort]\"";
          while ($targets = $statement->fetch(PDO::FETCH_ASSOC)) $csv .= ",=\"$targets[depid]\"";
          switch ($statement->rowCount()) {
            case 1: $csv .= ",";
            case 2: $csv .= ",";
            case 3: $csv .= ",";
            case 4: $csv .= ",";
            case 5: $csv .= ",";
          }
          $csv .= "\n";
        }
      }
      header("Content-type:application/vnd.ms-excel");
      header("Content-Disposition:filename=$vhSchoolId.csv");
      if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
        header("Cache-Control: maxage=3600");
        header("Prama: public");
      }
      echo mb_convert_encoding($csv, 'BIG5', 'UTF-8');
    } 
  } 
?>