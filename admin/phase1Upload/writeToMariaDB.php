<?php
/*
Array ( [projectRoot] => https://photo.taivs.tp.edu.tw/enter42/admin [name] => 廖啟良 [optype] => 1 [secondsBrowserTimezoneOffset] => 28800 [secondsServerTimezoneOffset] => 28800 )
Array ( [uploadedFile] => Array ( [name] => B103_1各校年級總分排名(總檔)_3年級.XLS [type] => application/vnd.ms-excel [tmp_name] => /volume1/@tmp/phph8r2eN [error] => 0 [size] => 321460 ) )
Array ( [fileType] => 2 )
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
    if ($fileType != 'xls' && $fileType != 'xlsx') {
      $_SESSION['msg'] = 'danger:上傳檔案格式錯誤！請上傳 xls 或 xlsx 格式之檔案。';
      header("Location: $_SESSION[projectRoot]/main/");
    } 
    else if ($fileType == 'xls') $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
         else                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $workSheetFile = $reader->load($workFile);

    error_reporting(E_ALL ^ E_NOTICE);
    // 至此檔案已上傳成功，準備將資料寫入資料庫

    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		// 逐行讀取資料 
    $numOfRows = 0;
    $sql = "INSERT INTO phase1 (id, depId, regStation, pass, passStation, payStation, unPassReason, fee) " .
           "VALUES ( right(:id,6), :depId, :regStation, :pass, :passStation, :payStation, :unPassReason, :fee);";
		$statement = $pdo->prepare($sql);
    // 讀取活頁簿檔案中的作用工作表
    $sheetData = $workSheetFile->getActiveSheet()->toArray(null,true,true,true);
    //print_r ($sheetData);
    foreach ($sheetData as $i => $record) {
      if ($i == 1) continue;
      //print_r ($record);
      //echo '<hr>';
      
      $pass = ($record['K'] == 'V' ? 1 : 0);

      $statement->bindParam(':id',              $record['A'], PDO::PARAM_STR, 6);
      $statement->bindParam(':depId',           $record['F'], PDO::PARAM_STR, 6);
      $statement->bindParam(':regStation',      $record['J'], PDO::PARAM_STR, 20);
      $statement->bindParam(':pass',            $pass,        PDO::PARAM_INT, 1);
      $statement->bindParam(':passStation',     $record['L'], PDO::PARAM_STR, 20);
      $statement->bindParam(':payStation',      $record['M'], PDO::PARAM_STR, 20);
      $statement->bindParam(':unPassReason',    $record['N'], PDO::PARAM_STR, 40);
      $statement->bindParam(':fee',             $record['O'], PDO::PARAM_INT, 3);
      
      if ($statement->execute()) $numOfRows++;
      else {
        $errorInfoArray = $statement->errorInfo();
        $_SESSION['msg'] = "danger: 發生錯誤！代碼：$errorInfoArray[0]/$errorInfoArray[1]<br>訊息：$errorInfoArray[2]";
        header("Location: $_SESSION[projectRoot]/main/");
        exit();
      }
    }

    // 刪除上傳檔案
    unlink ($workFile);

    $_SESSION['msg'] = "success: 完成 $numOfRows 筆資料上傳。";
    header("Location: $_SESSION[projectRoot]/main/");
  }
}
?>