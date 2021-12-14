<?php
/* 系統底層變數定義區 */

// 連結資料庫的主機，一般來說應該設定 localhost 就行了。
$host = 'localhost:3307';
// 資料庫名稱
$databaseName = 'enter42'; 

// 專案根目錄
$rootPath = "https://$_SERVER[SERVER_NAME]" . $_SERVER['REQUEST_URI'];

/********************************************************************************
** 後臺程式操作資料庫時所使用的帳號、密碼                                   
** 全域權限：無。                                                             
** 操作資料庫enter42權限：全域 -> select,                                     
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
$importentMessage = '本系統查詢資料僅供參考，所有資訊以「111學年度科技校院四年級及專科學校二年制聯合甄選委員會」所發行之簡章及網站資料為準。';

// 高中職學校名稱
$vhSchool = '臺北市立大安高工<span style="color: yellow;">日間部</span>';
$vhSchoolId = '1251';

// 校內IP群
$inSchoolIp[] = '210.70.131.*';
$inSchoolIp[] = '10.0.*.*';
$inSchoolIp[] = '127.0.0.1';

////////////以上資料請依各項環境因素自行修改，以使系統順利執行////////////////
?>