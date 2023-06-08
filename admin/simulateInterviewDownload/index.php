<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    include '../menu.php';
    include '../../config.ini.php';
    // 資料庫連線，刪除人員介面選單用
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
    //$csv = "班級,座號,姓名,統測報名類別,聯絡電話1,聯絡電話2,校系科組學程代碼1,校系科組學程代碼2,校系科組學程代碼3,校系科組學程代碼4,校系科組學程代碼5,校系科組學程代碼6\n";
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // 標題列
    $sheet->setCellValue('A1','班級');
    $sheet->setCellValue('B1','座號');
    $sheet->setCellValue('C1','姓名');
    $sheet->setCellValue('D1','類別');
    $sheet->setCellValue('E1','聯絡電話一');
    $sheet->setCellValue('F1','聯絡電話二');
    $sheet->setCellValue('G1','校系一');  
    $sheet->setCellValue('H1','校系二');
    $sheet->setCellValue('I1','校系三');
    $sheet->setCellValue('J1','校系四');
    $sheet->setCellValue('K1','校系五');
    $sheet->setCellValue('L1','校系六');
  
    $rowCounter = 1;
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
        $rowCounter++;
        $sheet->setCellValue('A'.$rowCounter,$student['classTitle']);
        $seatNo = substr($student['stuid'],-2);
        $sheet->setCellValue('B'.$rowCounter,$seatNo);
        $sheet->setCellValue('C'.$rowCounter,$student['stuName']);
        $sheet->setCellValue('D'.$rowCounter,$student['examSort']);
        $sheet->setCellValue('E'.$rowCounter,$student['phone1']);
        $sheet->setCellValue('F'.$rowCounter,$student['phone2']);
        $column = 0x47;
        $colCounter = 0;
        while ($targets = $statement->fetch(PDO::FETCH_ASSOC)) $sheet->setCellValue(strval(chr($column++).$rowCounter), $targets['depIdTitle']);
      }
    }
  
    // 準備下載檔案
    $fileName = '專業問題模擬面試名單(報名結果)_'.date('Ymd_His').'.xlsx';
    if (file_exists($frileName)) unlink($fileName);
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);
  
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName .'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($fileName));
    ob_clean();
    flush();
    readfile($fileName);
  
    // 刪除檔案
    unlink($fileName);
  } 
} 
?>