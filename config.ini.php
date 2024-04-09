<?php
/* 系統底層變數定義區 */

// 連結資料庫的主機，一般來說應該設定 localhost 就行了。
$host = 'localhost:3307';
// 資料庫名稱
$databaseName = 'enter42'; 

/********************************************************************************
** 後臺程式操作資料庫時所使用的帳號、密碼                                   
** 全域權限：無。                                                             
** 操作資料庫enter42權限：全域 -> select, insert                                     
** 		操作資料表control, operator, student, class權限：insert, update, delete 
**		操作資料表TVERETarget, TVEREOperateRecord, TVEREStatic權限：delete      
********************************************************************************/
$opId = 'enter42Operator';
$opPw = 'pB7/9hwd3g';

/*****************************************************************
** 學生介面操作資料庫時所使用的帳號、密碼
** 全域權限：無。
** 操作資料庫enter42權限：全域 -> select,
**     操作資料表TVEREOperateRecord權限：insert
**     操作資料表TVERETarget, TVEREStatic權限：insert, delete
**     操作資料表student.password, student.simInterView, student.phone1, student.phone2 權限：update
*****************************************************************/
$stuId = 'enter42Student';
$stuPw = 'kI8/I]peUF';

// 查詢介面操作資料庫時所使用的帳號、密碼
$guestId = 'enter42Guest';
$guestPw = 'vY1_u(zF-x';        


// 重要訊息文字
$importentMessage = '本系統查詢資料僅供參考，所有資訊以「113學年度科技校院四年級及專科學校二年制聯合甄選委員會」所發行之簡章及網站資料為準。';

// 高中職學校名稱
$vhSchool = '臺北市立大安高工<span style="color: yellow;">日間部</span>';
// 轉出檔所冠之學校代碼。
$vhSchoolId = '1251';

// 預選校系上限
$maxTargets = 6;

// 是否要輔導室功能
$serverName = $_SERVER['SERVER_NAME'];
if ( $serverName == 'photo.taivs.tp.edu.tw' || $serverName == 'yy33.us' ) $extraFunction = true;
else $extraFunction = false;

// reCAPTCHA 開關
if ( $serverName == 'photo.taivs.tp.edu.tw' ) $reCAPTCHA = true;
else $reCAPTCHA = false;

$reCAPTCHA = false;

////////////以上資料請依各項環境因素自行修改，以使系統順利執行////////////////
function mysqlDateTime2PHPTimeInteger($mysqlDateTimeString) {
  $year = substr($mysqlDateTimeString,0,4);
  $month = substr($mysqlDateTimeString,5,2);
  $day = substr($mysqlDateTimeString,8,2);
  $hour = substr($mysqlDateTimeString,11,2);
  $minute = substr($mysqlDateTimeString,14,2);
  $second = substr($mysqlDateTimeString,-2);
  return mktime((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year);
}

function weekDay($value) {
  if (!isset($value)) return '--';
  switch ($value) {
    case 0: $returnString = '一'; break;
    case 1: $returnString = '二'; break;
    case 2: $returnString = '三'; break;
    case 3: $returnString = '四'; break;
    case 4: $returnString = '五'; break;
    case 5: $returnString = '六'; break;
    case 6: $returnString = '日'; break;
  }
  return '(' . $returnString . ')';
}		
?>