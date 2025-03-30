<?php
/*
$_SESSION:
  Array ( [projectRoot] => https://yy33.us/enter42/admin 
          [name] => 廖啟良 \
          [optype] => 1 
          [secondsBrowserTimezoneOffset] => 28800 
          [secondsServerTimezoneOffset] => 28800 
        )
$_FILES:
  Array ( [uploadedFile] => Array ( [name] => 112_125_臺北市立大安高工_統測落點預測.xlsx 
                                    [type] => application/vnd.openxmlformats-officedocument.spreadsheetml.sheet 
                                    [tmp_name] => /volume1/@tmp/phpLq3WRt 
                                    [error] => 0 [size] => 69988 
                                  ) 
        )
*/
// 使用 phpSpreadSheet 來讀取 Excel 檔
require_once 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../config.ini.php';
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
else {
  session_start();
  if (!isset($_FILES)) { $_SESSION['msg'] = "danger:未選擇上傳檔案！請重新操作。"; header ("Location: $_SESSION[projectRoot]/main/"); }
  if ( !isset( $_SESSION['name'] ) ) header( "Location: https://$_SERVER[SERVER_NAME]".dirname( $_SERVER['SCRIPT_NAME'] ).'/../' );
  else { 
    if ( $_FILES['uploadedFile']['error'] ) {
      switch ( $_FILES['uploadedFile']['error'] ) {
        case 1: $_SESSION['msg'] =  'danger:檔案超過 php.ini 中的設定值。'; break;
        case 2: $_SESSION['msg'] =  'danger:檔案超過 form 中的設定值。'; break;
        case 3: $_SESSION['msg'] =  'danger:僅部份檔案上傳！'; break;
        case 4: $_SESSION['msg'] =  'danger:沒有檔案上傳！'; break;
        case 6: $_SESSION['msg'] =  'danger:找不到 upload_tmp_dir 設定之資料夾'; break;
        case 7: $_SESSION['msg'] =  'danger:伺服器端存檔失敗！'; break;
        case 8: $_SESSION['msg'] =  'danger:上傳失敗！'; break;
      }
      header("Location: $_SESSION[projectRoot]/main/");
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
    $numOfRows = 0;
    $statement = $pdo->prepare( 
      "INSERT INTO possibilityDepartments (id, examSort, chinese, english, math, prof1, prof2, departments) ".
      "VALUES ( right(:id,6), :examSort, :chinese, :english, :math, :prof1, :prof2, :departments);" 
    );
    // 讀取活頁簿檔案中的作用工作表
    $sheetData = $workSheetFile->getActiveSheet()->toArray(null,true,true,true);
    //print_r ($sheetData);
    
    foreach ($sheetData as $i => $student) {
      if ($i == 1 or $i == 2) continue;
      $statement->bindParam(':id',          $student['B'], PDO::PARAM_STR,  11);
      $statement->bindParam(':examSort',    $student['C'], PDO::PARAM_STR,   2);
      $statement->bindParam(':chinese',     $student['E'], PDO::PARAM_STR,  16);
      $statement->bindParam(':english',     $student['F'], PDO::PARAM_STR,  16);
      $statement->bindParam(':math',        $student['G'], PDO::PARAM_STR,  16);
      $statement->bindParam(':prof1',       $student['H'], PDO::PARAM_STR,  16);
      $statement->bindParam(':prof2',       $student['I'], PDO::PARAM_STR,  16);
      $statement->bindParam(':departments', $student['J'], PDO::PARAM_STR, 512);
      $statement->execute();
      $errorInfo = $statement->errorInfo();
      if ($errorInfo[0] != '00000') { $_SESSION['msg'] = "danger: 寫入資料庫發生錯誤，代碼：$errorInfo[0].<br>.訊息代碼：$errorInfo[1]，訊息：$errorInfo[2]。"; header("Location: $_SESSION[projectRoot]/main/"); }
      $numOfRows++;
    }
    
    // 刪除上傳檔案
    unlink ($workFile);

    $_SESSION['msg'] = "success: 上傳 $numOfRows 筆資料完畢，請從「檢視學生資料」功能進行檢視。";
    header("Location: $_SESSION[projectRoot]/main/");
  }
}
?>