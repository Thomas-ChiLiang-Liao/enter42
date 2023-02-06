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
    if ($fileType != 'xls' && $fileType != 'xlsx') {
      $_SESSION['msg'] = 'danger:上傳檔案格式錯誤！請上傳 xls 或 xlsx 格式之檔案。';
      header("Location: $_SESSION[projectRoot]/main/");
    } else if ($fileType == 'xls') $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
           else                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $workSheetFile = $reader->load($workFile);

    error_reporting(E_ALL ^ E_NOTICE);
    // 至此檔案已上傳成功，準備將資料寫入資料庫

    // 資料庫連線
		$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);
		
		// 逐行讀取資料 
    $numOfClasses = 0;
    
		$statement = $pdo->prepare( "INSERT INTO class (id, title) VALUES (:id, :title);" );
    
    // 讀取活頁簿檔案中的作用工作表	
    $sheetData = $workSheetFile->getActiveSheet()->toArray(null,true,true,true);
    foreach ($sheetData as $i => $class) {
      if ($i == 1) continue;

			$statement->bindParam(':id', 		$class['A'], PDO::PARAM_STR, 	3);
			$statement->bindParam(':title',	$class['B'], PDO::PARAM_STR, 12);

      if ($statement->execute()) $numOfClasses++;
      else {
        $_SESSION['msg'] = 'danger:發生錯誤，錯誤代碼：'.$statement->errorInfo()[1].'('.$statement->errorInfo()[0].')<br>訊息：'.$statement->errorInfo()[2];
        header ("Location: $_SESSION[projectRoot]/main/");
      }
    }
    // 刪除上傳的檔案
    unlink ($workDir.'/'.$workFile);
    //將資料庫設定成起始狀態
     
    $_SESSION['msg'] = "success:已上傳 $numOfClasses 筆班級資料。";
     
    header("Location: $_SESSION[projectRoot]/main/");     
  }
}
?>