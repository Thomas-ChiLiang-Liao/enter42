<?php
include 'config.ini.php';
  $pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
  // 所有學生
  $sql = "SELECT " . 
           "class.title AS classTitle, " .
           "student.id AS stuid, ".
           "student.name AS stuName, ".
           "CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort, ".
           "student.phone1 AS phone1, ".
           "student.phone2 AS phone2 ".
         "FROM student ".
         "LEFT JOIN class ON LEFT(student.id,3) = class.id ".
         "LEFT JOIN TVETExamSort ON student.examSort = TVETExamSort.id ".
         "WHERE simInterView ".
         "ORDER BY student.id ASC;";
  $studentsListQuery = $pdo->query($sql);
  $csv = "班級,座號,姓名,統測報名類別,聯絡電話1,聯絡電話2,校系科組學程代碼1,校系科組學程代碼2,校系科組學程代碼3,校系科組學程代碼4,校系科組學程代碼5,校系科組學程代碼6\n";
  $studentCounter = 0;
  while ($student = $studentsListQuery->fetch(PDO::FETCH_ASSOC)) {
    $sql =  "SELECT ".
              "CONCAT(TVEREDepartment.id, TVERESchool.title, TVEREDepartment.title) AS depIdTitle ".
            "FROM TVERETarget ".
            "LEFT JOIN TVERESchool ON TVERESchool.id = SUBSTRING(TVERETarget.id,7,3) ".
            "LEFT JOIN TVEREDepartment ON TVEREDepartment.id = RIGHT(TVERETarget.id,6) ".
            "WHERE LEFT(TVERETarget.id,6) = :stuid ".
            "ORDER BY TVEREDepartment.id ASC;";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':stuid', $student['stuid'], PDO::PARAM_STR, 6);
    if (!$statement->execute()) {
      $errorInfo = $statement->errorInfo();
      echo "讀取資料發生錯誤！代碼：$errorInfo[0]/$errorInfo[1]<br>訊息：$errorInfo[2]";
      exit();
    } else {
      // 如果此生沒有預選就跳過不處理
      if ($statement->rowCount() == 0) continue;
      $studentCounter++;
      $csv .= "=\"$student[classTitle]\"";
      $csv .= ",\"".substr($student['stuid'],-2)."\"";
      $csv .= ",\"$student[stuName]\"";
      $csv .= ",=\"$student[examSort]\"";
      $csv .= ",=\"$student[phone1]\"";
      $csv .= ",=\"$student[phone2]\"";
      while ($targets = $statement->fetch(PDO::FETCH_ASSOC)) $csv .= ",=\"$targets[depIdTitle]\"";
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
  header("Content-Disposition:filename=temp.csv");
  if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
    header("Cache-Control: maxage=3600");
    header("Prama: public");
  }
  echo mb_convert_encoding($csv, 'BIG5', 'UTF-8');
?>