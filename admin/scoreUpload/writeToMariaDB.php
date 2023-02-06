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
		$statement = $pdo->prepare("UPDATE student SET score = :score, scoreG = :scoreG WHERE id = :id;");
    // 讀取活頁簿檔案中的作用工作表
    $sheetData = $workSheetFile->getActiveSheet()->toArray(null,true,true,true);
    //print_r ($sheetData);
    foreach ($sheetData as $i => $student) {
      if ($i == 1) continue;
      $score  = "{ \"chinese\": $student[G], \"english\": $student[J], \"math\": $student[M], \"pro1\": $student[P], \"pro2\": ";
      $scoreG = "{ \"chinese\": $student[H], \"english\": $student[K], \"math\": $student[N], \"pro1\": $student[Q], \"pro2\": ";
      $examSort = substr($student['E'],0,2);
      switch ($examSort) {
        case '01':
        case '02':
        case '03':
        case '04':
        case '05':
        case '06':
        case '07':
        case '08':
        case '10':
        case '11':
        case '12':
        case '13':
        case '14':
        case '15':
        case '16':
        case '17':
        case '18':
        case '19':
        case '20':
          $score  .= "{\"B$examSort\": $student[S] } }";
          $scoreG .= "{\"B$examSort\": $student[T] } }";
          break;
        case '09':  
        case '21':
          $score  .= "{\"B09\": $student[S], \"B21\": $student[S] } }";
          $scoreG .= "{\"B09\": $student[T], \"B21\": $student[T] } }";
          break;
        case '51':
          $score  .= "{\"B03\": $student[S], \"B04\": $student[V] } }";
          $scoreG .= "{\"B03\": $student[T], \"B04\": $student[W] } }";
          break;
        case '52':
          $score  .= "{\"B12\": $student[S], \"B13\": $student[V] } }";
          $scoreG .= "{\"B12\": $student[T], \"B13\": $student[W] } }";
          break;
        case '53':
          $score  .= "{\"B09\": $student[S], \"B15\": $student[V], \"B21\": $student[S] } }";
          $scoreG .= "{\"B09\": $student[T], \"B15\": $student[W], \"B21\": $student[T] } }";
          break;
        case '54':
          $score  .= "{\"B09\": $student[S], \"B16\": $student[V], \"B21\": $student[S] } }";
          $scoreG .= "{\"B09\": $student[T], \"B16\": $student[W], \"B21\": $student[T] } }";
          break;
        case '55':
          $score  .= "{\"B15\": $student[S], \"B16\": $student[V] } }";
          $scoreG .= "{\"B15\": $student[T], \"B16\": $student[W] } }";
          break;
        case '56':
          $score  .= "{\"B09\": $student[S], \"B15\": $student[V], \"B16\": $student[Y], \"B21\": $student[S] } }";
          $scoreG .= "{\"B09\": $student[T], \"B15\": $student[W], \"B16\": $student[Z], \"B21\": $student[T] } }";
          break;
      } 
      //echo $score.' G-> '.$scoreG.'<hr>';  
      $id = substr($student['A'],-6);
      $statement->bindParam(':score', $score, PDO::PARAM_STR);
      $statement->bindParam(':scoreG', $scoreG, PDO::PARAM_STR);
      $statement->bindParam(':id', $id, PDO::PARAM_STR, 6);
      $statement->execute();
      $errorInfo = $statement->errorInfo();
      if ($errorInfo[0] != '00000') { $_SESSION['msg'] = "danger: 寫入資料庫發生錯誤，代碼：$errorInfo[0].<br>.訊息代碼：$errorInfo[1]，訊息：$errorInfo[2]。"; header("Location: $_SESSION[projectRoot]/main/"); }
      $numOfRows++;
    }

    // 刪除上傳檔案
    unlink ($workFile);

    $_SESSION['msg'] = "success: 完成 $numOfRows 筆成績上傳。請從「檢視學生資料」功能檢視上傳結果。";
    header("Location: $_SESSION[projectRoot]/main/");
  }
}
?>