<?php
/*
$_SESSION['projectRoot'] => https://photo.taivs.tp.edu.tw/enter42/admin

$_POST['secondsBrowserTimezoneOffset] => 28800
$_POST['userId']
$_POST['userName']
$_POST['userPw']
*/
// 強制使用 https 連線
if ( !isset( $_SERVER['HTTPS'] ) OR ( $_SERVER['HTTPS'] != 'on' ) ) header("Location: https://$_SERVER[SERVER_NAME]" . dirname( $_SERVER['SCRIPT_NAME'] . '/' ) );

session_start();

// 引入檔
include "../config.ini.php";

// 消除可能的 SQL Injection
foreach ($_POST as $i => $data) {
  $data = str_replace('"','',$data);
  $data = str_replace("'","",$data);
  $_POST[$i] = $data;
}
 
// 建立資料庫連線 & 選擇資料庫
$pdo = new PDO("mysql:host=$host;dbname=$databaseName;charset=utf8", $opId, $opPw);

// SQL指令
$sql = 'SELECT * FROM operator WHERE id= :userId AND password = :userPw;';

$statement = $pdo->prepare($sql);
$statement->bindParam(':userId',$_POST['userId'],PDO::PARAM_STR, 1);
$statement->bindParam(':userPw',$_POST['userPw'],PDO::PARAM_STR, 40);

// 執行SQL指令
$statement->execute();

echo $statement->rowCount();

if ( $statement->rowCount() == 1 ) {
  // 登入成功。讀取登入者的資料。
  $record = $statement->fetch(PDO::FETCH_ASSOC);
  
  // 寫入 $_SESSION 中，但密碼不保存。
  $_SESSION['name'] = $record['name'];
  $_SESSION['optype'] = $record['optype'];
  $_SESSION['secondsBrowserTimezoneOffset'] = $_POST['secondsBrowserTimezoneOffset'];
  $_SESSION['secondsServerTimezoneOffset'] = date("Z", time());
  
  header("Location: $_SESSION[projectRoot]/main/");
} else 
// 登入失敗，重新登入。
header("Location: $_SESSION[projectRoot]/index.php?loginFailed");  
?>