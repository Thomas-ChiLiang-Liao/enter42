<?php
// 使用 phpSpreadSheet 來讀取 Excel 檔
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../config.ini.php';
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    if ( $_FILES['uploadedFile']['error'] ) {
      switch ( $_FILES['uploadedFile']['error'] ) {
        case 1: echo '檔案超過 php.ini 中的設定值。';	break;
        case 2: echo '檔案超過 form 中的設定值。'; break;
        case 3: echo '僅部份檔案上傳！'; break;
        case 4: echo '沒有檔案上傳！'; break;
        case 6: echo '找不到 upload_tmp_dir 設定之資料夾'; break;
        case 7: echo '伺服器端存檔失敗！'; break;
        case 8: echo '上傳失敗！'; break;
      }
    }
    // 將上傳檔由系統暫存區移到工作資料夾
    $uploadedFile = explode('.', $_FILES['uploadedFile']['name']);
    $workDir = dirname($_SERVER['SCRIPT_FILENAME']);
    $fileType = $uploadedFile[count($uploadedFile)-1];
    $workFile = 'uploadTemp.' . $fileType;
    move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $workDir . '/' . $workFile);

    // 以 PhpSpreadsheet 來讀取檔案
    if ($fileType == 'xls') $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
    else                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $workSheetFile = $reader->load($workFile);

    error_reporting(E_ALL ^ E_NOTICE);
    // 至此檔案已上傳成功，準備將資料寫入資料庫

    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		// 逐行讀取資料 
    $numOfStudents = 0;
		$statement = $pdo->prepare(
			"INSERT INTO student ( id, name, password, examArea, examSort, examID, examPlace, score, scoreG, simInterView, phone1, phone2, pw_backup ) " .
			"VALUES ( right(:id,6), :name, sha1(:password), :examArea, :examSort, :examID, :examPlace, null, null, false, :phone1, :phone2, sha1(:password) );"
		);
    // 讀取活頁簿檔案中的作用工作表
    $sheetData = $workSheetFile->getActiveSheet()->toArray(null,true,true,true);
    foreach ($sheetData as $i => $student) {
      if ($i == 1) continue;

      $student['F'] = ( strlen($student['F']) == 1 ? '0' : '' ) . $student['F'];
			$student['N'] = ( strlen($student['N']) == 8 ? '02' : ( strlen($student['N']) == 9 ? '0' : '' ) ) . $student['N'];
			$student['O'] = ( strlen($student['O']) == 8 ? '02' : ( strlen($student['O']) == 9 ? '0' : '' ) ) . $student['O'];
      $student['Z'] = '%' . $student['Z'] . '&';
			
			$statement->bindParam(':id', 				$student['A'], PDO::PARAM_STR, 	6);
			$statement->bindParam(':name',			$student['H'], PDO::PARAM_STR, 10);
			$statement->bindParam(':password',	$student['Z'], PDO::PARAM_STR, 12);
			$statement->bindParam(':examArea',	$student['D'], PDO::PARAM_STR,  2);	
			$statement->bindParam(':examSort',	$student['F'], PDO::PARAM_STR,  2);
			$statement->bindParam(':examID',		$student['C'], PDO::PARAM_STR,  8);
			$statement->bindParam(':examPlace',	$student['E'], PDO::PARAM_STR, 30);
			$statement->bindParam(':phone1',		$student['N'], PDO::PARAM_STR, 15);
			$statement->bindParam(':phone2',		$student['O'], PDO::PARAM_STR, 15);  

      if ($statement->execute()) $numOfStudents++;
      else {
        $_SESSION['msg'] = 'danger:發生錯誤，錯誤代碼：'.$statement->errorInfo()[1].'('.$statement->errorInfo()[0].')<br>訊息：'.$statement->errorInfo()[2];;
        header ("Location: $_SESSION[projectRoot]/main/");
      }
    }
    // 刪除上傳的檔案
    unlink ($workDir.'/'.$workFile);
    //將資料庫設定成起始狀態
    // 將 enter42.TVERETarget 清空
    $statement = $pdo->query('DELETE FROM TVERETarget WHERE 1;');
    // 將 enter42.TVEREOperateRecord 清空
    $statement = $pdo->query('DELETE FROM TVEREOperateRecord WHERE 1;');
    // 將 enter42.TVEREStatic 清空
    $statement= $pdo->query('DELETE FROM TVEREStatic WHERE 1;');
    $_SESSION['msg'] = "success:已上傳 $numOfStudents 筆學生資料。";
    
    header("Location: $_SESSION[projectRoot]/main/");     
  }
}
?>